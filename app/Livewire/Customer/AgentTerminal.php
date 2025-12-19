<?php

namespace App\Livewire\Customer;

use App\Enums\RemoteCommandStatus;
use App\Enums\RemoteCommandType;
use App\Enums\TerminalSessionStatus;
use App\Enums\TerminalType;
use App\Models\Agent;
use App\Models\TerminalSession;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use InvalidArgumentException;
use JsonException;
use Livewire\Attributes\On;
use Livewire\Component;

class AgentTerminal extends Component
{
    public Agent $agent;

    public ?string $activeSessionId = null;

    public string $terminalInput = '';

    public string $terminalType = 'powershell';

    public ?int $userSessionId = null;

    public bool $showCreateModal = false;

    /**
     * Mount the component.
     */
    public function mount(Agent $agent): void
    {
        $this->agent = $agent;

        // Auto-select first active session
        $activeSession = $this->agent->terminalSessions()->active()->first();
        if ($activeSession) {
            $this->activeSessionId = $activeSession->id;
        }
    }

    /**
     * Get the sessions property.
     */
    public function getSessionsProperty(): Collection
    {
        return $this->agent->terminalSessions()
            ->orderByDesc('created_at')
            ->limit(20)
            ->get();
    }

    /**
     * Get the active session property.
     */
    public function getActiveSessionProperty(): ?TerminalSession
    {
        if (! $this->activeSessionId) {
            return null;
        }

        return $this->agent->terminalSessions()->find($this->activeSessionId);
    }

    /**
     * Get the terminal logs property.
     */
    public function getTerminalLogsProperty(): Collection
    {
        if (! $this->activeSession) {
            return collect();
        }

        return $this->activeSession->logs()
            ->orderBy('created_at')
            ->limit(500)
            ->get();
    }

    /**
     * Get the terminal types property.
     */
    public function getTerminalTypesProperty(): array
    {
        return collect(TerminalType::cases())
            ->mapWithKeys(fn ($type) => [$type->value => $type->label()])
            ->toArray();
    }

    /**
     * Select a session.
     */
    public function selectSession(string $sessionId): void
    {
        $this->activeSessionId = $sessionId;
        $this->dispatch('terminal-changed');
    }

    /**
     * Open the create modal.
     */
    public function openCreateModal(): void
    {
        $this->terminalType = 'powershell';
        $this->userSessionId = null;
        $this->showCreateModal = true;
    }

    /**
     * Close the create modal.
     */
    public function closeCreateModal(): void
    {
        $this->showCreateModal = false;
    }

    /**
     * Create a session.
     */
    public function createSession(): void
    {
        $sessionId = (string) Str::uuid();

        $session = $this->agent->terminalSessions()->create([
            'id' => $sessionId,
            'type' => $this->terminalType,
            'user_session_id' => $this->userSessionId,
            'status' => TerminalSessionStatus::RUNNING,
            'started_at' => now(),
            'created_by' => auth()->id(),
        ]);

        // Pošli session UUID v command, config jako JSON v url
        $this->agent->remoteCommands()->create([
            'type' => RemoteCommandType::TERMINAL_CREATE,
            'command' => $sessionId,
            'url' => json_encode([
                'type' => $this->terminalType,
                'user_session_id' => $this->userSessionId,
            ]),
            'status' => RemoteCommandStatus::PENDING,
            'created_by' => auth()->id(),
        ]);

        $this->activeSessionId = $session->id;
        $this->closeCreateModal();
        $this->dispatch('terminal-created');
    }

    /**
     * Send input to the active session.
     */
    public function sendInput(): void
    {
        if (! $this->activeSession || ! $this->activeSession->isActive()) {
            return;
        }

        $input = trim($this->terminalInput);
        if (empty($input)) {
            return;
        }

        // Log input
        $this->activeSession->logInput($input);

        // Create command for agent
        $this->agent->remoteCommands()->create([
            'type' => RemoteCommandType::TERMINAL_INPUT,
            'command' => $this->activeSessionId,
            'url' => $input,
            'status' => RemoteCommandStatus::PENDING,
            'created_by' => auth()->id(),
        ]);

        $this->terminalInput = '';
        $this->dispatch('input-sent');
    }

    /**
     * Send Ctrl+C to the active session.
     */
    public function sendCtrlC(): void
    {
        if (! $this->activeSession || ! $this->activeSession->isActive()) {
            return;
        }

        $this->activeSession->logInput('^C');

        $this->agent->remoteCommands()->create([
            'type' => RemoteCommandType::TERMINAL_INPUT,
            'command' => $this->activeSessionId,
            'url' => "\x03",
            'status' => RemoteCommandStatus::PENDING,
            'created_by' => auth()->id(),
        ]);

        $this->dispatch('ctrl-c-sent');
    }

    /**
     * Request output from the active session.
     */
    public function requestOutput(): void
    {
        if (! $this->activeSession) {
            return;
        }

        // Nevolej output pro zavřenou session
        if (! $this->activeSession->isActive()) {
            return;
        }

        // Nevolej output pro session mladší než 10 sekund (počkej až agent vytvoří session)
        if ($this->activeSession->created_at->diffInSeconds(now()) < 10) {
            return;
        }

        // Nevolej output pokud už existuje pending output command pro tuto session
        $pendingExists = $this->agent->remoteCommands()
            ->where('type', RemoteCommandType::TERMINAL_OUTPUT)
            ->where('command', $this->activeSessionId)
            ->whereIn('status', [RemoteCommandStatus::PENDING, RemoteCommandStatus::SENT])
            ->exists();

        if ($pendingExists) {
            return;
        }

        $this->agent->remoteCommands()->create([
            'type' => RemoteCommandType::TERMINAL_OUTPUT,
            'command' => $this->activeSessionId,
            'status' => RemoteCommandStatus::PENDING,
            'created_by' => auth()->id(),
        ]);
    }

    /**
     * Close a session.
     */
    public function closeSession(?string $sessionId = null): void
    {
        $sessionId = $sessionId ?? $this->activeSessionId;
        if (! $sessionId) {
            return;
        }

        $session = $this->agent->terminalSessions()->find($sessionId);
        if (! $session || ! $session->isActive()) {
            return;
        }

        $this->agent->remoteCommands()->create([
            'type' => RemoteCommandType::TERMINAL_CLOSE,
            'command' => $sessionId,
            'status' => RemoteCommandStatus::PENDING,
            'created_by' => auth()->id(),
        ]);

        $session->close();

        if ($this->activeSessionId === $sessionId) {
            $nextSession = $this->agent->terminalSessions()->active()->first();
            $this->activeSessionId = $nextSession?->id;
        }

        $this->dispatch('terminal-closed');
    }

    /**
     * Close the terminal modal.
     */
    #[On('closeTerminal')]
    public function close(): void
    {
        $this->dispatch('closeTerminal')->to('customer.agents');
    }

    /**
     * Parse terminal output.
     *
     * @param  string  $output
     *
     * @throws JsonException
     *
     * @return string
     */
    public function parseTerminalOutputContent(string $output): string
    {
        dump($output);

        if ($this->is_json_array($output)) {
            $parsed = $this->parseTerminalJsonOutput($output);

            dump($parsed);

            if(! empty($parsed['output']))
            return $parsed['output'];
        }

        return $output;
    }

    /**
     * Parse terminal output JSON string into an array.
     *
     * @param string $content JSON string to parse
     *
     * @throws InvalidArgumentException When content is empty or not a valid array/object
     * @throws JsonException When JSON parsing fails
     *
     * @return array<string, mixed> Parsed terminal output
     */
    public function parseTerminalJsonOutput(string $content): array
    {
        if (empty($content)) {
            throw new InvalidArgumentException('Output cannot be empty');
        }

        $decoded = json_decode($content, true, 512, JSON_THROW_ON_ERROR);

        if (!is_array($decoded)) {
            throw new InvalidArgumentException('Decoded output is not an array');
        }
        return $decoded;
    }

    /**
     * Check if a string is valid JSON.
     *
     * @param  string  $value
     *
     * @return bool
     */
    public function is_json(string $value): bool
    {
        if (function_exists('json_validate')) {
            return json_validate($value);
        }

        json_decode($value);
        return json_last_error() === JSON_ERROR_NONE;
    }

    /**
     *
     *
     * @param  string  $value
     *
     * @return bool
     */
    public function is_json_array(string $value): bool
    {
        if (!$this->is_json($value)) {
            return false;
        }

        return is_array(json_decode($value, true));
    }

    /**
     * Render the component.
     */
    public function render(): View
    {
        return view('livewire.customer.agent-terminal', [
            'sessions' => $this->sessions,
            'activeSession' => $this->activeSession,
            'terminalLogs' => $this->terminalLogs,
            'terminalTypes' => $this->terminalTypes,
        ]);
    }
}
