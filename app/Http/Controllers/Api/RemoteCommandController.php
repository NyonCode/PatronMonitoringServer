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
     *
     * @param string $uuid
     *
     * @return Agent
     */
    private function getAgent(string $uuid): Agent
    {
        return Agent::where('uuid', $uuid)->firstOrFail();
    }

    /**
     * Store results from agent
     *
     * @param Request $request
     * @param string $uuid
     *
     * @return JsonResponse
     */
    public function storeResults(Request $request, string $uuid): JsonResponse
    {
        Log::info('Agent id: ' . $uuid);
        Log::info('Store results', $request->all());

        $agent = $this->getAgent($uuid);

        $validated = $request->validate([
            'results' => ['required', 'array'],
            'results.*.command_id' => ['required', 'integer'],
            'results.*.status' => ['required', 'string'],
            'results.*.output' => ['nullable', 'string'],
            'results.*.error' => ['nullable', 'string'],
            'results.*.exit_code' => ['nullable', 'integer'],
        ]);

        $processed = 0;
        foreach ($validated['results'] as $result) {
            $command = RemoteCommand::where('id', $result['command_id'])
                ->where('agent_id', $agent->id)
                ->first();

            if (!$command) continue;

            $status = RemoteCommandStatus::tryFrom($result['status']);
            if ($status === RemoteCommandStatus::COMPLETED) {
                $command->markAsCompleted($result['output'] ?? null, $result['exit_code'] ?? null);
            } elseif ($status === RemoteCommandStatus::FAILED) {
                $command->markAsFailed($result['error'] ?? null, $result['exit_code'] ?? null);
            }

            if ($command->type->isTerminalCommand() && !empty($result['output'])) {
                $this->processTerminalOutput($command, $result);
            }
            $processed++;
        }

        return response()->json([
            'status' => 'ok',
            'processed' => $processed
        ]);
    }

    /**
     * Create terminal session
     *
     * @param  Request  $request
     * @param  string  $uuid
     *
     * @return JsonResponse
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

        // OPRAVA: Pošli session UUID v command, config jako JSON v url
        $agent->remoteCommands()->create([
            'type' => RemoteCommandType::TERMINAL_CREATE,
            'command' => $sessionId,  // <-- Session UUID, NE typ terminálu!
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
     *
     * @param  Request  $request
     * @param  string  $uuid
     * @param  string  $sessionId
     *
     * @return JsonResponse
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

    public function listTerminals(string $uuid): JsonResponse
    {
        $agent = $this->getAgent($uuid);
        $sessions = $agent->getActiveTerminalSessions();

        return response()->json([
            'sessions' => $sessions->map(fn(TerminalSession $s) => $s->toApiFormat()),
        ]);
    }

    public function getTerminalHistory(Request $request, string $uuid, string $sessionId): JsonResponse
    {
        $agent = $this->getAgent($uuid);
        $service = app(TerminalPollingService::class);
        $result = $service->getHistory($agent, $sessionId, $request->integer('limit', 100));

        return response()->json($result);
    }

    private function processTerminalOutput(RemoteCommand $command, array $result): void
    {
        $sessionId = $command->command;
        if (!$sessionId) return;

        $session = TerminalSession::find($sessionId);
        if (!$session) return;

        if (!empty($result['output'])) {
            $session->logOutput($result['output']);
        }
    }
}
