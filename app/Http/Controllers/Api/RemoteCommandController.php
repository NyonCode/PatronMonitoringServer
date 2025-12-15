<?php

namespace App\Http\Controllers\Api;

use App\Enums\RemoteCommandStatus;
use App\Enums\RemoteCommandType;
use App\Enums\TerminalSessionStatus;
use App\Enums\TerminalType;
use App\Http\Controllers\Controller;
use App\Models\Agent;
use App\Models\RemoteCommand;
use App\Models\TerminalSession;
use App\Services\TerminalPollingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Enum;

class RemoteCommandController extends Controller
{
    /**
     * Get agent by UUID helper
     */
    private function getAgent(string $uuid): Agent
    {
        return Agent::where('uuid', $uuid)->firstOrFail();
    }

    /**
     * Store results from agent
     */
    public function storeResults(Request $request, string $uuid): JsonResponse
    {
        $agent = $this->getAgent($uuid);

        // Transformuj PascalCase na snake_case (C# posílá PascalCase)
        $results = collect($request->input('results', []))->map(function ($item) {
            return [
                'command_id' => $item['command_id'] ?? $item['CommandId'] ?? null,
                'status' => $item['status'] ?? $item['Status'] ?? null,
                'output' => $item['output'] ?? $item['Output'] ?? null,
                'error' => $item['error'] ?? $item['Error'] ?? null,
                'exit_code' => $item['exit_code'] ?? $item['ExitCode'] ?? null,
            ];
        })->toArray();

        $validated = validator(['results' => $results], [
            'results' => ['required', 'array'],
            'results.*.command_id' => ['required', 'integer'],
            'results.*.status' => ['required', 'string'],
            'results.*.output' => ['nullable', 'string'],
            'results.*.error' => ['nullable', 'string'],
            'results.*.exit_code' => ['nullable', 'integer'],
        ])->validate();

        $processed = 0;
        foreach ($validated['results'] as $result) {
            $command = RemoteCommand::where('id', $result['command_id'])
                ->where('agent_id', $agent->id)
                ->first();

            if (!$command) {
                continue;
            }

            $status = RemoteCommandStatus::tryFrom($result['status']);
            if ($status === RemoteCommandStatus::COMPLETED) {
                $command->markAsCompleted($result['output'] ?? null, $result['exit_code'] ?? null);
            } elseif ($status === RemoteCommandStatus::FAILED) {
                $command->markAsFailed($result['error'] ?? null, $result['exit_code'] ?? null);
            }

            // Zpracuj terminal output
            if ($command->type->isTerminalCommand() && !empty($result['output'])) {
                $this->processTerminalOutput($command, $result);
            }

            $processed++;
        }

        return response()->json([
            'status' => 'ok',
            'processed' => $processed,
        ]);
    }

    /**
     * Create terminal session
     */
    public function createTerminal(Request $request, string $uuid): JsonResponse
    {
        $agent = $this->getAgent($uuid);

        $validated = $request->validate([
            'type' => ['nullable', new Enum(TerminalType::class)],
            'user_session_id' => ['nullable', 'integer'],
        ]);

        $sessionId = (string) Str::uuid();
        $type = $validated['type'] ?? TerminalType::POWERSHELL;
        $userSessionId = $validated['user_session_id'] ?? null;

        $session = $agent->terminalSessions()->create([
            'id' => $sessionId,
            'type' => $type,
            'user_session_id' => $userSessionId,
            'status' => TerminalSessionStatus::RUNNING,
            'started_at' => now(),
            'created_by' => $request->user()?->id,
        ]);

        // Pošli session UUID v command, config jako JSON v url
        $agent->remoteCommands()->create([
            'type' => RemoteCommandType::TERMINAL_CREATE,
            'command' => $sessionId,
            'url' => json_encode([
                'type' => $type instanceof TerminalType ? $type->value : $type,
                'user_session_id' => $userSessionId,
            ]),
            'status' => RemoteCommandStatus::PENDING,
            'created_by' => $request->user()?->id,
        ]);

        return response()->json(['status' => 'ok', 'session' => $session->toApiFormat()], 201);
    }

    /**
     * Send terminal input
     */
    public function sendTerminalInput(Request $request, string $uuid, string $sessionId): JsonResponse
    {
        $agent = $this->getAgent($uuid);
        $session = $agent->terminalSessions()->where('id', $sessionId)->active()->firstOrFail();

        $validated = $request->validate([
            'input' => ['required', 'string'],
            'send_ctrl_c' => ['nullable', 'boolean'],
        ]);

        $session->logInput($validated['input']);

        $agent->remoteCommands()->create([
            'type' => RemoteCommandType::TERMINAL_INPUT,
            'command' => $sessionId,
            'url' => $validated['input'],
            'status' => RemoteCommandStatus::PENDING,
            'created_by' => $request->user()?->id,
        ]);

        return response()->json(['status' => 'ok']);
    }

    /**
     * Get terminal output
     */
    public function getTerminalOutput(Request $request, string $uuid, string $sessionId): JsonResponse
    {
        $agent = $this->getAgent($uuid);
        $agent->terminalSessions()->where('id', $sessionId)->firstOrFail();

        $agent->remoteCommands()->create([
            'type' => RemoteCommandType::TERMINAL_OUTPUT,
            'command' => $sessionId,
            'status' => RemoteCommandStatus::PENDING,
            'created_by' => $request->user()?->id,
        ]);

        return response()->json(['status' => 'ok']);
    }

    /**
     * Close terminal session
     */
    public function closeTerminal(string $uuid, string $sessionId): JsonResponse
    {
        $agent = $this->getAgent($uuid);
        $session = $agent->terminalSessions()->where('id', $sessionId)->active()->firstOrFail();

        $agent->remoteCommands()->create([
            'type' => RemoteCommandType::TERMINAL_CLOSE,
            'command' => $sessionId,
            'status' => RemoteCommandStatus::PENDING,
        ]);

        $session->close();

        return response()->json(['status' => 'ok']);
    }

    /**
     * List active terminals
     */
    public function listTerminals(string $uuid): JsonResponse
    {
        $agent = $this->getAgent($uuid);
        $sessions = $agent->getActiveTerminalSessions();

        return response()->json([
            'sessions' => $sessions->map(fn(TerminalSession $s) => $s->toApiFormat()),
        ]);
    }

    /**
     * Get terminal history
     */
    public function getTerminalHistory(Request $request, string $uuid, string $sessionId): JsonResponse
    {
        $agent = $this->getAgent($uuid);
        $service = app(TerminalPollingService::class);
        $result = $service->getHistory($agent, $sessionId, $request->integer('limit', 100));

        return response()->json($result);
    }

    /**
     * Process terminal output from command result
     */
    private function processTerminalOutput(RemoteCommand $command, array $result): void
    {
        // Pro terminal_create - aktualizuj session pokud agent vrátil jiné info
        if ($command->type === RemoteCommandType::TERMINAL_CREATE) {
            $sessionId = $command->command;
            $session = TerminalSession::find($sessionId);

            if ($session && !empty($result['output'])) {
                try {
                    $outputData = json_decode($result['output'], true);
                    // Můžeme logovat nebo aktualizovat session info
                    Log::debug('Terminal created', ['session' => $sessionId, 'output' => $outputData]);
                } catch (\Exception $e) {
                    // Ignore JSON parse errors
                }
            }
            return;
        }

        // Pro terminal_output - ulož output do logu
        if ($command->type === RemoteCommandType::TERMINAL_OUTPUT) {
            $sessionId = $command->command;
            $session = TerminalSession::find($sessionId);

            if ($session && !empty($result['output'])) {
                try {
                    $outputData = json_decode($result['output'], true);
                    $terminalOutput = $outputData['Output'] ?? $outputData['output'] ?? null;

                    if (!empty($terminalOutput)) {
                        $session->logOutput($terminalOutput);
                    }
                } catch (\Exception $e) {
                    // Pokud není JSON, ulož přímo
                    $session->logOutput($result['output']);
                }
            }
            return;
        }
    }
}
