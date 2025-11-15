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

    // Indikátory dostupnosti dat
    public bool $hasCurrentData = false;

    public bool $hasHistoricalData = false;

    public ?string $suggestedPeriod = null;

    public ?string $suggestedPeriodLabel = null;

    // Pro inline editaci
    public bool $editingName = false;

    public string $editName = '';

    private bool $isOnline = true;

    protected MetricsChartService $metricsService;

    public function boot(MetricsChartService $metricsService): void
    {
        $this->metricsService = $metricsService;
    }

    public function mount(Agent $agent): void
    {
        $this->agent = $agent;
        $this->editName = $this->getEditName();
        $this->checkOnlineStatus();
        $this->loadData();
        $this->findPeriodWithData();
    }

    public function updatedPeriod(): void
    {
        $this->loadData();
        $this->dispatch('periodChanged');
    }

    /**
     * Kontrola online stavu agenta
     */
    private function checkOnlineStatus(): void
    {
        if (! $this->agent->last_seen_at) {
            $this->isOnline = false;

            return;
        }

        $threshold = now()->subMinutes(5);
        $this->isOnline = $this->agent->last_seen_at->greaterThan($threshold);
    }

    /**
     * Načtení dat s kontrolou dostupnosti
     */
    public function loadData(): void
    {
        $this->checkOnlineStatus();

        // Inicializuj prázdné hodnoty
        $this->currentMetrics = [
            'cpu' => 0,
            'ram' => 0,
            'gpu' => 0,
        ];
        $this->hasCurrentData = false;

        // Zkus načíst aktuální metriky
        $metrics = $this->metricsService->getCurrentMetrics($this->agent);
        if ($metrics && ($metrics['cpu'] > 0 || $metrics['ram'] > 0 || $metrics['gpu'] > 0)) {
            $this->currentMetrics = $metrics;
            $this->hasCurrentData = true;
        }

        // Historická data
        $this->chartData = $this->metricsService->getChartData($this->agent, $this->period);
        $this->hasHistoricalData = ! empty($this->chartData['labels']) && count($this->chartData['labels']) > 0;

        // Diskový status
        $this->diskStatus = $this->metricsService->getDiskStatus($this->agent);
    }

    /**
     * Najde období s dostupnými daty
     */
    private function findPeriodWithData(): void
    {
        $this->suggestedPeriod = null;
        $this->suggestedPeriodLabel = null;

        // Pokud aktuální období má data, není třeba hledat
        if ($this->hasHistoricalData) {
            return;
        }

        $periods = [
            'hour' => 'Poslední hodina',
            'day' => 'Poslední den',
            'week' => 'Poslední týden',
            'month' => 'Poslední měsíc',
            'year' => 'Poslední rok',
        ];

        foreach ($periods as $period => $label) {
            if ($period === $this->period) {
                continue;
            }

            $data = $this->metricsService->getChartData($this->agent, $period);
            if (! empty($data['labels']) && count($data['labels']) > 0) {
                $this->suggestedPeriod = $period;
                $this->suggestedPeriodLabel = $label;
                break;
            }
        }
    }

    /**
     * Přepne na období s daty
     */
    public function switchToPeriodWithData(): void
    {
        if ($this->suggestedPeriod) {
            $this->period = $this->suggestedPeriod;
            $this->loadData();
            $this->dispatch('periodChanged');
        }
    }

    /**
     * Začne editaci názvu
     */
    public function startEditingName(): void
    {
        $this->editingName = true;
        $this->editName = $this->getEditName();
    }

    /**
     * Uloží nový název
     */
    public function saveName(): void
    {
        $this->agent->update([
            'pretty_name' => $this->editName,
        ]);

        $this->editingName = false;
        $this->dispatch('name-updated');
    }

    /**
     * Zruší editaci názvu
     */
    public function cancelEditName(): void
    {
        $this->editingName = false;
        $this->editName = $this->getEditName();
    }

    /**
     * Optimalizovaný polling - aktualizuje pouze pokud je agent online
     */
    public function refreshMetrics(): void
    {
        $this->agent->refresh();
        $this->checkOnlineStatus();

        // Pokud je agent offline, nepřenačítej data tak často
        if (! $this->isOnline) {
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
            $hasData = ! empty($newChartData['labels']) && count($newChartData['labels']) > 0;

            if ($hasData !== $this->hasHistoricalData) {
                $this->hasHistoricalData = $hasData;
            }

            if ($hasData) {
                $this->chartData = $newChartData;
            }
        }
    }

    /**
     * Získá formátované síťové informace
     */
    public function getNetworkInfo(): ?array
    {
        $network = $this->agent->network;

        if (! $network) {
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

    /**
     * Uzavře detail
     */
    public function closeDetail(): void
    {
        $this->dispatch('closeDetail')->to('customer.agents');
    }

    public function render(): View|Factory|\Illuminate\View\View
    {
        return view('livewire.customer.agent-detail', [
            'networkInfo' => $this->getNetworkInfo(),
            'isOnline' => $this->isOnline,
        ]);
    }

    private function getEditName(): string
    {
        if (empty($this->agent->pretty_name)) {
            return $this->agent->hostname;
        }

        return $this->agent->pretty_name;
    }
}
