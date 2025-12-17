<?php

namespace App\Livewire\Customer;

use App\Enums\RemoteCommandStatus;
use App\Enums\RemoteCommandType;
use App\Models\Agent;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class AgentQuickCommand extends Component
{
    public Agent $agent;

    public string $command = '';

    public string $commandType = 'powershell';

    public ?string $lastOutput = null;

    public ?string $lastError = null;

    public bool $isRunning = false;

    /**
     * Mount the component.
     */
    public function mount(Agent $agent): void
    {
        $this->agent = $agent;
    }

    /**
     * Execute the command.
     */
    public function executeCommand(): void
    {
        if (empty(trim($this->command))) {
            return;
        }

        $this->agent->remoteCommands()->create([
            'type' => $this->commandType === 'powershell'
                ? RemoteCommandType::POWERSHELL
                : RemoteCommandType::EXEC,
            'command' => $this->command,
            'status' => RemoteCommandStatus::PENDING,
            'created_by' => auth()->id(),
        ]);

        $this->isRunning = true;
        $this->lastOutput = null;
        $this->lastError = null;
        $this->command = '';

        $this->dispatch('command-sent');
    }

    /**
     * Check the result of the command.
     */
    public function checkResult(): void
    {
        $lastCommand = $this->agent->remoteCommands()
            ->whereIn('type', [RemoteCommandType::POWERSHELL, RemoteCommandType::EXEC])
            ->latest()
            ->first();

        if ($lastCommand && $lastCommand->status->isFinished()) {
            $this->isRunning = false;
            $this->lastOutput = $lastCommand->output;
            $this->lastError = $lastCommand->error;
        }
    }

    /**
     * Render the component.
     */
    public function render(): View
    {
        return view('livewire.customer.agent-quick-command');
    }
}
