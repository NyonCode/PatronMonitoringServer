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

    protected MetricsChartService $metricsService;

    public function boot(MetricsChartService $metricsService): void
    {
        $this->metricsService = $metricsService;
    }

    public function mount(Agent $agent): void
    {
        $this->agent = $agent;
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

    public function render(): View|Factory|\Illuminate\View\View
    {
        return view('livewire.customer.agent-detail');
    }
}
