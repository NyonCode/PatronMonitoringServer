<?php

namespace App\Livewire\Customer;

use App\Models\Agent;
use App\Services\MetricsChartService;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class AgentDetail extends Component
{
    public Agent $agent;
    public string $period = 'day';
    public array $chartData = [];
    public array $currentMetrics = [];
    public array $diskStatus = [];

    // Pro inline editaci
    public bool $editingName = false;
    public string $editName = '';

    protected MetricsChartService $metricsService;

    public function boot(MetricsChartService $metricsService): void
    {
        $this->metricsService = $metricsService;
    }

    public function mount(Agent $agent): void
    {
        $this->agent = $agent;
        $this->editName = $agent->pretty_name ?? $agent->hostname;
        $this->loadData();
    }

    public function updatedPeriod(): void
    {
        $this->loadData();
    }

    public function loadData(): void
    {
        $this->chartData = $this->metricsService->getChartData($this->agent, $this->period);
        $this->currentMetrics = $this->metricsService->getCurrentMetrics($this->agent);
        $this->diskStatus = $this->metricsService->getDiskStatus($this->agent);
    }

    /**
     * Začne editaci názvu.
     */
    public function startEditingName(): void
    {
        $this->editingName = true;
        $this->editName = $this->agent->pretty_name ?? $this->agent->hostname;
    }

    /**
     * Uloží nový název.
     */
    public function saveName(): void
    {
        $this->validate([
            'editName' => 'required|string|max:255',
        ]);

        $this->agent->update([
            'pretty_name' => $this->editName,
        ]);

        $this->editingName = false;

        $this->dispatch('name-updated');
    }

    /**
     * Zruší editaci názvu.
     */
    public function cancelEditName(): void
    {
        $this->editingName = false;
        $this->editName = $this->agent->pretty_name ?? $this->agent->hostname;
    }

    /**
     * Polling pro live aktualizaci (každých 5 sekund).
     */
    public function refreshMetrics(): void
    {
        $this->currentMetrics = $this->metricsService->getCurrentMetrics($this->agent);

        // Pokud zobrazujeme poslední hodinu, aktualizuj i graf
        if ($this->period === 'hour') {
            $this->chartData = $this->metricsService->getChartData($this->agent, 'hour');
        }
    }

    /**
     * Získá formátované síťové informace.
     */
    public function getNetworkInfo(): ?array
    {
        $network = $this->agent->network;

        if (!$network) {
            return null;
        }

        return [
            'ip_address' => $network->ip_address,
            'subnet_mask' => $network->subnet_mask,
            'gateway' => $network->gateway,
            'dns' => is_array($network->dns) ? implode(', ', $network->dns) : $network->dns,
            'mac_address' => $network->mac_address,
        ];
    }

    public function render(): View|Factory|\Illuminate\View\View
    {
        return view('livewire.customer.agent-detail', [
            'networkInfo' => $this->getNetworkInfo(),
        ]);
    }
}
