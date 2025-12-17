<?php

namespace App\Livewire\Customer;

use App\Enums\RemoteCommandStatus;
use App\Enums\RemoteCommandType;
use App\Models\Agent;
use App\Models\RemoteCommand;
use App\Services\ParsedOutput;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Livewire\Attributes\On;
use Livewire\Component;

class AgentCommands extends Component
{
    public Agent $agent;

    public string $commandType = '';
    public string $commandText = '';
    public string $commandUrl = '';

    public string $filterStatus = '';
    public string $filterType = '';

    public bool $showCreateModal = false;

    protected $rules = [
        'commandType' => 'required|string',
        'commandText' => 'nullable|string|max:10000',
        'commandUrl' => 'nullable|string|max:2048',
    ];

    public function mount(Agent $agent): void
    {
        $this->agent = $agent;
    }

    public function getCommandsProperty(): Collection
    {
        return $this->agent->remoteCommands()
            ->with('creator')
            ->when($this->filterStatus, fn($q) => $q->where('status', $this->filterStatus))
            ->when($this->filterType, fn($q) => $q->where('type', $this->filterType))
            ->orderByDesc('created_at')
            ->limit(50)
            ->get();
    }

    public function getCommandTypesProperty(): array
    {
        return collect(RemoteCommandType::cases())
            ->filter(fn($type) => !$type->isTerminalCommand())
            ->mapWithKeys(fn($type) => [$type->value => $type->label()])
            ->toArray();
    }

    public function getStatusOptionsProperty(): array
    {
        return collect(RemoteCommandStatus::cases())
            ->mapWithKeys(fn($status) => [$status->value => $status->label()])
            ->toArray();
    }

    public function openCreateModal(): void
    {
        $this->reset(['commandType', 'commandText', 'commandUrl']);
        $this->showCreateModal = true;
    }

    public function closeCreateModal(): void
    {
        $this->showCreateModal = false;
    }

    public function createCommand(): void
    {
        $this->validate();

        $this->agent->remoteCommands()->create([
            'type' => $this->commandType,
            'command' => $this->commandText ?: null,
            'url' => $this->commandUrl ?: null,
            'status' => RemoteCommandStatus::PENDING,
            'created_by' => auth()->id(),
        ]);

        $this->closeCreateModal();
        $this->dispatch('command-created');
    }

    public function cancelCommand(int $commandId): void
    {
        $command = $this->agent->remoteCommands()->find($commandId);

        if ($command && $command->canBeCancelled()) {
            $command->update([
                'status' => RemoteCommandStatus::CANCELLED,
                'completed_at' => now(),
            ]);
            $this->dispatch('command-cancelled');
        }
    }

    public function quickCommand(string $type): void
    {
        $this->agent->remoteCommands()->create([
            'type' => $type,
            'status' => RemoteCommandStatus::PENDING,
            'created_by' => auth()->id(),
        ]);

        $this->dispatch('command-created');
    }

    public function clearFilters(): void
    {
        $this->reset(['filterStatus', 'filterType']);
    }

    /**
     * Parse command output.
     *S
     * @param  RemoteCommand  $command
     *
     * @return ParsedOutput|null
     */
    public function parseCommandOutput(RemoteCommand $command): ?ParsedOutput
    {
        return $command->parsed_output;
    }

    public function render(): View
    {
        return view('livewire.customer.agent-commands', [
            'commands' => $this->commands,
            'commandTypes' => $this->commandTypes,
            'statusOptions' => $this->statusOptions,
        ]);
    }
}
