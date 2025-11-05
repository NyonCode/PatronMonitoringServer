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

    public function mount(Agent $agent): void
    {
        $this->agent = $agent;
        $this->agentLog = $this->agent->log;
    }

    /**
     * Optimalizovaný polling - aktualizuje pouze pokud je agent online
     */
    public function refreshMetrics(): void
    {
        $this->agent->refresh();
        $this->checkOnlineStatus();

        // Pokud je agent offline, nepřenačítej data tak často
        if (!$this->isOnline) {
            // Zkontroluj pouze jednou za delší dobu, jestli se neobjevila nová data
            if (rand(1, 12) === 1) { // Každou minutu (5s * 12)
                $this->findPeriodWithData();
            }
            return;
        }

        // Aktualizuj aktuální metriky
        $newMetrics = $this->metricsService->getCurrentMetrics($this->agent);

        // Kontrola zda jsou validní data
        if ($newMetrics && ($newMetrics['cpu'] > 0 || $newMetrics['ram'] > 0 || $newMetrics['gpu'] > 0)) {
            $this->hasCurrentData = true;
            if ($this->currentMetrics !== $newMetrics) {
                $this->currentMetrics = $newMetrics;
            }
        } else {
            $this->hasCurrentData = false;
            $this->currentMetrics = [
                'cpu' => 0,
                'ram' => 0,
                'gpu' => 0,
            ];
        }

        // Disk status
        $diskStatus = $this->metricsService->getDiskStatus($this->agent);
        if ($this->diskStatus !== $diskStatus) {
            $this->diskStatus = $diskStatus;
        }

        // Aktualizuj graf data pouze pro poslední hodinu
        if ($this->period === 'hour') {
            $newChartData = $this->metricsService->getChartData($this->agent, 'hour');
            $hasData = !empty($newChartData['labels']) && count($newChartData['labels']) > 0;

            if ($hasData !== $this->hasHistoricalData) {
                $this->hasHistoricalData = $hasData;
            }

            if ($hasData) {
                $this->chartData = $newChartData;
            }
        }
    }

    public function render(): View|Factory|\Illuminate\View\View
    {
        return view('livewire.customer.agent-log', ['agentLog' => $this->agentLog] );
    }
}
