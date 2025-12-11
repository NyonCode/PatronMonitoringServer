<?php

namespace App\Services;

use App\Models\Agent;
use App\Models\AgentMetricDaily;
use App\Models\AgentMetricHourly;
use App\Models\AgentSystemMetric;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class MetricsChartService
{
    /**
     * Získá data pro graf podle časového období.
     */
    public function getChartData(Agent $agent, string $period = 'day'): array
    {
        return match ($period) {
            'hour' => $this->getLastHourData($agent),
            'day' => $this->getLastDayData($agent),
            'week' => $this->getLastWeekData($agent),
            'month' => $this->getLastMonthData($agent),
            'year' => $this->getLastYearData($agent),
            default => $this->getLastDayData($agent),
        };
    }

    /**
     * Data za poslední hodinu (surová data, vysoká frekvence).
     */
    public function getLastHourData(Agent $agent): array
    {
        $metrics = AgentSystemMetric::where('agent_id', $agent->id)
            ->where('recorded_at', '>', now()->subHour())
            ->orderBy('recorded_at')
            ->get(['recorded_at', 'cpu_usage_percent', 'ram_usage_percent', 'gpu_usage_percent']);

        return $this->formatMetrics($metrics, 'recorded_at');
    }

    /**
     * Data za poslední den (surová data, minutové vzorky).
     */
    public function getLastDayData(Agent $agent): array
    {
        $metrics = AgentSystemMetric::where('agent_id', $agent->id)
            ->where('recorded_at', '>', now()->subDay())
            ->orderBy('recorded_at')
            ->get(['recorded_at', 'cpu_usage_percent', 'ram_usage_percent', 'gpu_usage_percent']);

        return $this->formatMetrics($metrics, 'recorded_at');
    }

    /**
     * Data za poslední týden (hodinové agregace).
     */
    public function getLastWeekData(Agent $agent): array
    {
        $metrics = AgentMetricHourly::where('agent_id', $agent->id)
            ->where('hour_start', '>', now()->subWeek())
            ->orderBy('hour_start')
            ->get();

        return $this->formatAggregatedMetrics($metrics, 'hour_start');
    }

    /**
     * Data za poslední měsíc (hodinové agregace).
     */
    public function getLastMonthData(Agent $agent): array
    {
        $metrics = AgentMetricHourly::where('agent_id', $agent->id)
            ->where('hour_start', '>', now()->subMonth())
            ->orderBy('hour_start')
            ->get();

        return $this->formatAggregatedMetrics($metrics, 'hour_start');
    }

    /**
     * Data za poslední rok (denní agregace).
     */
    public function getLastYearData(Agent $agent): array
    {
        $metrics = AgentMetricDaily::where('agent_id', $agent->id)
            ->where('date', '>', now()->subYear())
            ->orderBy('date')
            ->get();

        return $this->formatAggregatedMetrics($metrics, 'date');
    }

    /**
     * Formátuje surové metriky pro graf.
     */
    public function formatMetrics(Collection $metrics, string $timeField): array
    {
        return [
            'labels' => $metrics->pluck($timeField)->map(fn ($time) => Carbon::parse($time)->setTimezone('Europe/Prague')->format('H:i')
            )->toArray(),
            'datasets' => [
                [
                    'label' => 'CPU %',
                    'data' => $metrics->pluck('cpu_usage_percent')->toArray(),
                    'borderColor' => 'rgb(239, 68, 68)',
                    'backgroundColor' => 'rgba(239, 68, 68, 0.2)',
                    'fill' => true,
                    'tension' => 0.4,
                ],
                [
                    'label' => 'RAM %',
                    'data' => $metrics->pluck('ram_usage_percent')->toArray(),
                    'borderColor' => 'rgb(59, 130, 246)',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.2)',
                    'fill' => true,
                    'tension' => 0.4,
                ],
                [
                    'label' => 'GPU %',
                    'data' => $metrics->pluck('gpu_usage_percent')->toArray(),
                    'borderColor' => 'rgb(34, 197, 94)',
                    'backgroundColor' => 'rgba(34, 197, 94, 0.2)',
                    'fill' => true,
                    'tension' => 0.4,
                ],
            ],
        ];
    }

    /**
     * Formátuje agregované metriky pro graf.
     */
    public function formatAggregatedMetrics(Collection $metrics, string $timeField): array
    {
        $format = $timeField === 'date' ? 'd.m.' : 'H:i';

        return [
            'labels' => $metrics->pluck($timeField)->map(fn ($time) => Carbon::parse($time)->setTimezone('Europe/Prague')->format($format)
            )->toArray(),
            'datasets' => [
                [
                    'label' => 'CPU průměr %',
                    'data' => $metrics->pluck('cpu_avg')->toArray(),
                    'borderColor' => 'rgb(239, 68, 68)',
                    'backgroundColor' => 'rgba(239, 68, 68, 0.2)',
                    'fill' => true,
                    'tension' => 0.4,
                ],
                [
                    'label' => 'RAM průměr %',
                    'data' => $metrics->pluck('ram_avg')->toArray(),
                    'borderColor' => 'rgb(59, 130, 246)',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.2)',
                    'fill' => true,
                    'tension' => 0.4,
                ],
                [
                    'label' => 'GPU průměr %',
                    'data' => $metrics->pluck('gpu_avg')->toArray(),
                    'borderColor' => 'rgb(34, 197, 94)',
                    'backgroundColor' => 'rgba(34, 197, 94, 0.2)',
                    'fill' => true,
                    'tension' => 0.4,
                ],
            ],
        ];
    }

    /**
     * Získá aktuální hodnoty metrik pro live zobrazení.
     */
    public function getCurrentMetrics(Agent $agent): array
    {
        $latest = $agent->metrics()->latest('recorded_at')->first();

        if (! $latest) {
            // Pokud agent nemá aktuální metriky, vrať poslední známé historické
            $fallback = AgentSystemMetric::where('agent_id', $agent->id)
                ->orderByDesc('recorded_at')
                ->first();

            if ($fallback) {
                return [
                    'cpu' => round($fallback->cpu_usage_percent, 2),
                    'ram' => round($fallback->ram_usage_percent, 2),
                    'gpu' => round($fallback->gpu_usage_percent ?? 0, 2),
                    'timestamp' => $fallback->recorded_at,
                ];
            }

            // Pokud ani to neexistuje, vrať nulové hodnoty
            return [
                'cpu' => 0,
                'ram' => 0,
                'gpu' => 0,
                'timestamp' => now(),
            ];
        }

        return [
            'cpu' => round($latest->cpu_usage_percent, 2),
            'ram' => round($latest->ram_usage_percent, 2),
            'gpu' => round($latest->gpu_usage_percent ?? 0, 2),
            'timestamp' => $latest->recorded_at,
        ];
    }

    /**
     * Získá stav disků.
     */
    public function getDiskStatus(Agent $agent): array
    {
        return $agent->disk()
            ->get()
            ->map(function ($disk) {
                return [
                    'name' => $disk->name,
                    'usage_percent' => round($disk->usage_percent, 2),
                    'free' => $this->formatBytes($disk->free),
                    'total' => $this->formatBytes($disk->total),
                    'free_bytes' => $disk->free,
                    'total_bytes' => $disk->total,
                ];
            })
            ->toArray();
    }

    /**
     * Formátuje byty na lidsky čitelný formát.
     */
    public function formatBytes(int|string $bytes, int $precision = 2): string
    {
        if (is_numeric($bytes)) {
            $units = ['B', 'KB', 'MB', 'GB', 'TB'];

            for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
                $bytes /= 1024;
            }

            return round($bytes, $precision).' '.$units[$i];
        }

        return $bytes;
    }
}
