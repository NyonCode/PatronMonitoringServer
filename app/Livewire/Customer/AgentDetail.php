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

    // Pro inline editaci
    public bool $editingName = false;
    public string $editName = '';

    // Cache pro optimalizaci
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
        if (!$this->agent->last_seen_at) {
            $this->isOnline = false;
            return;
        }

        $threshold = now()->subMinutes(5);
        $this->isOnline = $this->agent->last_seen_at->greaterThan($threshold);
    }

    /**
     * Načtení dat s optimalizací pro offline agenty
     */
    public function loadData(): void
    {
        $this->checkOnlineStatus();

        // Vždy načti historická data (i pro offline agenty)
        $this->chartData = $this->metricsService->getChartData($this->agent, $this->period);
        
        // Pro offline agenty načti poslední známé metriky
        $this->currentMetrics = $this->metricsService->getCurrentMetrics($this->agent);
        
        // Diskový status (i pro offline)
        $this->diskStatus = $this->metricsService->getDiskStatus($this->agent);
    }

    /**
     * Začne editaci názvu.
     */
    public function startEditingName(): void
    {
        $this->editingName = true;
        $this->editName = $this->getEditName();
    }

    /**
     * Uloží nový název.
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
     * Zruší editaci názvu.
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
        // Refresh agent z DB
        $this->agent->refresh();
        $this->checkOnlineStatus();

        // Pokud je agent offline, nepřenačítej data tak často
        if (!$this->isOnline) {
            return;
        }

        // Aktualizuj aktuální metriky
        $currentMetrics = $this->metricsService->getCurrentMetrics($this->agent);
        
        // Optimalizace - pouze pokud se změnily
        if ($this->currentMetrics !== $currentMetrics) {
            $this->currentMetrics = $currentMetrics;
        }

        // Disk status
        $diskStatus = $this->metricsService->getDiskStatus($this->agent);
        if ($this->diskStatus !== $diskStatus) {
            $this->diskStatus = $diskStatus;
        }

        // Aktualizuj graf data pouze pro poslední hodinu (častější změny)
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
