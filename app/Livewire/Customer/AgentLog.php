<?php

namespace App\Livewire\Customer;

use App\Models\Agent;
use App\Models\AgentLog as ModelsAgentLog;
use Livewire\Component;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;

class AgentLog extends Component
{
    public Agent $agent;
    public ?ModelsAgentLog $agentLog = null;

    // Filtry a vyhledávání
    public string $searchAgent = '';
    public string $searchSystem = '';
    public string $filterTypeAgent = '';
    public string $filterTypeSystem = '';
    public bool $autoRefresh = false;

    public function mount(Agent $agent): void
    {
        $this->agent = $agent;
        $this->loadLogs();
    }

    public function loadLogs(): void
    {
        $this->agentLog = $this->agent->log;
    }

    public function refreshLogs(): void
    {
        $this->loadLogs();
        $this->dispatch('logs-refreshed');
    }

    public function toggleAutoRefresh(): void
    {
        $this->autoRefresh = !$this->autoRefresh;
    }

    public function getFilteredAgentLogs(): array
    {
        if (!$this->agentLog || !$this->agentLog->agent_log) {
            return [];
        }

        $logs = collect($this->agentLog->agent_log);

        // Filtr podle typu
        if ($this->filterTypeAgent) {
            $logs = $logs->filter(fn($log) =>
                strtolower($log['EntryType'] ?? '') === strtolower($this->filterTypeAgent)
            );
        }

        // Vyhledávání
        if ($this->searchAgent) {
            $search = strtolower($this->searchAgent);
            $logs = $logs->filter(fn($log) =>
            str_contains(strtolower($log['Message'] ?? ''), $search)
            );
        }

        return $logs->sortByDesc('Time')->values()->toArray();
    }

    public function getFilteredSystemLogs(): array
    {
        if (!$this->agentLog || !$this->agentLog->system_logs) {
            return [];
        }

        $logs = collect($this->agentLog->system_logs);

        // Filtr podle typu
        if ($this->filterTypeSystem) {
            $logs = $logs->filter(fn($log) =>
                strtolower($log['EntryType'] ?? '') === strtolower($this->filterTypeSystem)
            );
        }

        // Vyhledávání
        if ($this->searchSystem) {
            $search = strtolower($this->searchSystem);
            $logs = $logs->filter(fn($log) =>
                str_contains(strtolower($log['Message'] ?? ''), $search) ||
                str_contains(strtolower($log['Source'] ?? ''), $search)
            );
        }

        return $logs->sortByDesc('Time')->values()->toArray();
    }

    public function clearFilters(): void
    {
        $this->searchAgent = '';
        $this->searchSystem = '';
        $this->filterTypeAgent = '';
        $this->filterTypeSystem = '';
    }

    public function render(): View|Factory|\Illuminate\View\View
    {
        return view('livewire.customer.agent-log', [
            'agentLogs' => $this->getFilteredAgentLogs(),
            'systemLogs' => $this->getFilteredSystemLogs(),
        ]);
    }
}
