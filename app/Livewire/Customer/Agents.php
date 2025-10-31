<?php

namespace App\Livewire\Customer;

use App\Models\Agent;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

class Agents extends Component
{
    use WithPagination;

    public string $search = '';
    public string $sortBy = 'hostname';
    public string $sortDirection = 'asc';
    public int $perPage = 10;

    // Modal state
    public ?int $selectedAgentId = null;
    public bool $showDetailModal = false;

    protected $queryString = [
        'search' => ['except' => ''],
        'sortBy' => ['except' => 'hostname'],
        'sortDirection' => ['except' => 'asc'],
        'perPage' => ['except' => 10],
    ];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function sortBy(string $column): void
    {
        if ($this->sortBy === $column) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $column;
            $this->sortDirection = 'asc';
        }
    }

    public function showDetail(int $agentId): void
    {
        $this->selectedAgentId = $agentId;
        $this->showDetailModal = true;
    }

    public function closeDetail(): void
    {
        $this->showDetailModal = false;
        $this->selectedAgentId = null;
    }

    #[Computed]
    public function agents(): LengthAwarePaginator|array
    {
        return Agent::query()
            ->with(['metrics' => function ($query) {
                $query->latest('recorded_at')->limit(10);
            }, 'disk'])
            ->when($this->search, function (Builder $query) {
                $query->where(function (Builder $q) {
                    $q->where('hostname', 'like', '%' . $this->search . '%')
                        ->orWhere('pretty_name', 'like', '%' . $this->search . '%')
                        ->orWhere('ip_address', 'like', '%' . $this->search . '%');
                });
            })
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate($this->perPage);
    }

    /**
     * Získá status agenta (online/offline).
     */
    public function getAgentStatus(Agent $agent): string
    {
        if (!$agent->last_seen_at) {
            return 'offline';
        }

        $lastSeen = $agent->last_seen_at;
        $threshold = now()->subMinutes(5); // 5 minut threshold

        return $lastSeen->greaterThan($threshold) ? 'online' : 'offline';
    }

    /**
     * Získá aktuální metriky agenta.
     */
    public function getCurrentMetrics(Agent $agent): array
    {
        $latest = $agent->metrics->first();

        if (!$latest) {
            return [
                'cpu' => 0,
                'ram' => 0,
                'gpu' => 0,
            ];
        }

        return [
            'cpu' => round($latest->cpu_usage_percent, 1),
            'ram' => round($latest->ram_usage_percent, 1),
            'gpu' => round($latest->gpu_usage_percent ?? 0, 1),
        ];
    }

    /**
     * Získá nejvíce zaplněný disk.
     */
    public function getMostUsedDisk(Agent $agent): ?array
    {
        $disk = $agent->disk->sortByDesc('usage_percent')->first();

        if (!$disk) {
            return null;
        }

        return [
            'name' => $disk->name,
            'usage_percent' => round($disk->usage_percent, 1),
            'free' => $this->formatBytes($disk->free),
            'size' => $this->formatBytes($disk->size),
        ];
    }

    /**
     * Získá data pro mini sparkline graf.
     */
    public function getSparklineData(Agent $agent): array
    {
        $metrics = $agent->metrics->take(10)->reverse()->values();

        return [
            'cpu' => $metrics->pluck('cpu_usage_percent')->toArray(),
            'ram' => $metrics->pluck('ram_usage_percent')->toArray(),
            'gpu' => $metrics->pluck('gpu_usage_percent')->toArray(),
        ];
    }

    /**
     * Formátuje byty na lidsky čitelný formát.
     * Akceptuje string nebo int, vždy konvertuje na int
     */
    public function formatBytes(int|string $bytes, int $precision = 1): string
    {
        // Konvertuj string na int pokud je potřeba
        if(is_int($bytes))
        {
            $bytes = (int) $bytes;

            $units = ['B', 'KB', 'MB', 'GB', 'TB'];

            for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
                $bytes /= 1024;
            }

            return round($bytes, $precision) . ' ' . $units[$i];
        }

        return $bytes;
    }

    public function render(): View|Factory|\Illuminate\View\View
    {
        return view('livewire.customer.agents');
    }
}
