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
    public string $period = 'hour';
    public array $chartData = [];
    public array $currentMetrics = [];
    public array $diskStatus = [];

    // Inline edit name
    public bool $editingName = false;
    public string $editName = '';

    // Inline edit interval
    public bool $editInterval = false;
    public ?int $editIntervalValue = null;

    protected MetricsChartService $metricsService;

    public function boot(MetricsChartService $metricsService): void
    {
        $this->metricsService = $metricsService;
    }

    public function mount(Agent $agent): void
    {
        $this->agent = $agent;
        $this->editName = $this->getEditName();
        $this->Data();
    }

    public function updatedPeriod(): void
    {
        $this->loadData();
        $this->dispatch('periodChanged');
    }

    public function loadData(): void
    {
        $this->chartData = $this->metricsService->getChartData($this->agent, $this->period);

        $this->currentMetrics = $this->metricsService->getCurrentMetrics($this->agent) ?? [
            'cpu' => 0,
            'ram' => 0,
            'gpu' => 0,
        ];

        $this->diskStatus = $this->metricsService->getDiskStatus($this->agent) ?? [];
    }

    public function startEditingName(): void
    {
        $this->editingName = true;
        $this->editName = $this->getEditName();
    }

    public function saveName(): void
    {
        $this->agent->update([
            'pretty_name' => $this->editName,
        ]);

        $this->editingName = false;
        $this->dispatch('name-updated');
    }

    public function cancelEditName(): void
    {
        $this->editingName = false;
        $this->editName = $this->getEditName();
    }

    /**
     * Polling pro živou aktualizaci - pouze metriky a případně graf
     */
    public function refreshMetrics(): void
    {
        // Aktualizuj metriky bez znovunačtení
        $newMetrics = $this->metricsService->getCurrentMetrics($this->agent);
        $newDiskStatus = $this->metricsService->getDiskStatus($this->agent);

        // Pokud se data změnila, aktualizuj
        if ($newMetrics !== $this->currentMetrics) {
            $this->currentMetrics = $newMetrics;
        }

        if ($newDiskStatus !== $this->diskStatus) {
            $this->diskStatus = $newDiskStatus;
        }

        // Aktualizuj graf data pouze pro poslední hodinu (častější změny)
        if ($this->period === 'hour') {
            $newChartData = $this->metricsService->getChartData($this->agent, 'hour');
            if ($newChartData !== $this->chartData) {
                $this->chartData = $newChartData;
                $this->dispatch('metrics-updated');
            }
        }
    }

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

    private function getEditName(): string
    {
        return empty($this->agent->pretty_name) ? $this->agent->hostname : $this->agent->pretty_name;
    }
}
