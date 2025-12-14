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
     *
     * @param Agent $agent
     *
     * @return void
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
     *
     * @return Collection
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
     *
     * @return ?TerminalSession
     */
    public function getActiveSessionProperty(): ?TerminalSession
    {
        if (!$this->activeSessionId) {
            return null;
        }

        return $this->agent->terminalSessions()->find($this->activeSessionId);
    }

    /**
     * Get the terminal log's property.
     *
     * @return Collection
     */
    public function getTerminalLogsProperty(): Collection
    {
        if (!$this->activeSession) {
            return collect();
        }

        return $this->activeSession->logs()
            ->orderBy('created_at')
            ->limit(500)
            ->get();
    }

    /**
     * Get the terminal types property.
     *
     * @return array
     */
    public function getTerminalTypesProperty(): array
    {
        return collect(TerminalType::cases())
            ->mapWithKeys(fn($type) => [$type->value => $type->label()])
            ->toArray();
    }

    /**
     * Select a session.
     *
     * @param string $sessionId
     *
     * @return void
     */
    public function selectSession(string $sessionId): void
    {
        $this->activeSessionId = $sessionId;
        $this->dispatch('terminal-changed');
    }

    /**
     * Open the create modal.
     *
     * @return void
     */
    public function openCreateModal(): void
    {
        $this->terminalType = 'powershell';
        $this->userSessionId = null;
        $this->showCreateModal = true;
    }

    /**
     * Close the create modal.
     *
     * @return void
     */
    public function closeCreateModal(): void
    {
        $this->showCreateModal = false;
    }

    /**
     * Create a session.
     *
     * @return void
     */
    public function createSession(): void
    {
        $session = $this->agent->terminalSessions()->create([
            'id' => Str::uuid(),
            'type' => $this->terminalType,
            'user_session_id' => $this->userSessionId,
            'status' => TerminalSessionStatus::RUNNING,
            'started_at' => now(),
            'created_by' => auth()->id(),
        ]);

        // Create command for agent
        $this->agent->remoteCommands()->create([
            'type' => RemoteCommandType::TERMINAL_CREATE,
            'command' => $this->terminalType,
            'url' => (string)($this->userSessionId ?? ''),
            'status' => RemoteCommandStatus::PENDING,
            'created_by' => auth()->id(),
        ]);

        $this->activeSessionId = $session->id;
        $this->closeCreateModal();
        $this->dispatch('terminal-created');
    }

    /**
     * Send input to the active session.
     *
     * @return void
     */
    public function sendInput(): void
    {
        if (!$this->activeSession || !$this->activeSession->isActive()) {
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
     *
     * @return void
     */
    public function sendCtrlC(): void
    {
        if (!$this->activeSession || !$this->activeSession->isActive()) {
            return;
        }

        $this->activeSession->logInput('^C');

        $this->agent->remoteCommands()->create([
            'type' => RemoteCommandType::TERMINAL_INPUT,
            'command' => $this->activeSessionId,
            'url' => "\x03", // Ctrl+C
            'status' => RemoteCommandStatus::PENDING,
            'created_by' => auth()->id(),
        ]);

        $this->dispatch('ctrl-c-sent');
    }

    /**
     * Request output from the active session.
     *
     * @return void
     */
    public function requestOutput(): void
    {
        if (!$this->activeSession) {
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
     * Close the active session.
     *
     * @param ?string $sessionId
     *
     * @return void
     */
    public function closeSession(?string $sessionId = null): void
    {
        $sessionId = $sessionId ?? $this->activeSessionId;
        if (!$sessionId) {
            return;
        }

        $session = $this->agent->terminalSessions()->find($sessionId);
        if (!$session || !$session->isActive()) {
            return;
        }

        // Create close command
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
     * Close the terminal.
     *
     * @return void
     */
    #[On('closeTerminal')]
    public function close(): void
    {
        $this->dispatch('closeTerminal')->to('customer.agents');
    }

    /**
     * Render the component.
     *
     * @return View
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
