<?php

namespace App\Console\Commands;

use App\Models\Agent;
use App\Models\AgentMetricDaily;
use App\Models\AgentMetricHourly;
use App\Models\AgentSystemMetric;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class AggregateMetricsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'metrics:aggregate {--hourly} {--daily}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Agreguje metriky do hodinových a denních tabulek pro rychlejší načítání grafů';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        if ($this->option('hourly')) {
            $this->aggregateHourly();
        }

        if ($this->option('daily')) {
            $this->aggregateDaily();
        }

        if (!$this->option('hourly') && !$this->option('daily')) {
            $this->aggregateHourly();
            $this->aggregateDaily();
        }

        return Command::SUCCESS;
    }

    /**
     * Agregace do hodinových statistik.
     */
    private function aggregateHourly(): void
    {
        $this->info('Agregace hodinových metrik...');

        // Najdi nejnovější agregovanou hodinu
        $lastAggregated = AgentMetricHourly::max('hour_start')
            ?? now()->subDays(7);

        $agents = Agent::all();

        foreach ($agents as $agent) {
            // Zpracuj hodiny od poslední agregace
            $metrics = AgentSystemMetric::where('agent_id', $agent->id)
                ->where('recorded_at', '>', $lastAggregated)
                ->select(
                    DB::raw('DATE_FORMAT(recorded_at, "%Y-%m-%d %H:00:00") as hour_start'),
                    DB::raw('AVG(cpu_usage_percent) as cpu_avg'),
                    DB::raw('MIN(cpu_usage_percent) as cpu_min'),
                    DB::raw('MAX(cpu_usage_percent) as cpu_max'),
                    DB::raw('AVG(ram_usage_percent) as ram_avg'),
                    DB::raw('MIN(ram_usage_percent) as ram_min'),
                    DB::raw('MAX(ram_usage_percent) as ram_max'),
                    DB::raw('AVG(gpu_usage_percent) as gpu_avg'),
                    DB::raw('MIN(gpu_usage_percent) as gpu_min'),
                    DB::raw('MAX(gpu_usage_percent) as gpu_max'),
                    DB::raw('COUNT(*) as sample_count')
                )
                ->groupBy('hour_start')
                ->get();

            foreach ($metrics as $metric) {
                AgentMetricHourly::updateOrCreate(
                    [
                        'agent_id' => $agent->id,
                        'hour_start' => $metric->hour_start,
                    ],
                    [
                        'cpu_avg' => $metric->cpu_avg,
                        'cpu_min' => $metric->cpu_min,
                        'cpu_max' => $metric->cpu_max,
                        'ram_avg' => $metric->ram_avg,
                        'ram_min' => $metric->ram_min,
                        'ram_max' => $metric->ram_max,
                        'gpu_avg' => $metric->gpu_avg,
                        'gpu_min' => $metric->gpu_min,
                        'gpu_max' => $metric->gpu_max,
                        'sample_count' => $metric->sample_count,
                    ]
                );
            }

            $this->info("Agent {$agent->hostname}: agregováno " . $metrics->count() . " hodin");
        }

        $this->info('Hodinové agregace dokončeny.');
    }

    /**
     * Agregace do denních statistik.
     */
    private function aggregateDaily(): void
    {
        $this->info('Agregace denních metrik...');

        $lastAggregated = AgentMetricDaily::max('date')
            ?? now()->subDays(30);

        $agents = Agent::all();

        foreach ($agents as $agent) {
            $metrics = AgentSystemMetric::where('agent_id', $agent->id)
                ->where('recorded_at', '>', $lastAggregated)
                ->select(
                    DB::raw('DATE(recorded_at) as date'),
                    DB::raw('AVG(cpu_usage_percent) as cpu_avg'),
                    DB::raw('MIN(cpu_usage_percent) as cpu_min'),
                    DB::raw('MAX(cpu_usage_percent) as cpu_max'),
                    DB::raw('AVG(ram_usage_percent) as ram_avg'),
                    DB::raw('MIN(ram_usage_percent) as ram_min'),
                    DB::raw('MAX(ram_usage_percent) as ram_max'),
                    DB::raw('AVG(gpu_usage_percent) as gpu_avg'),
                    DB::raw('MIN(gpu_usage_percent) as gpu_min'),
                    DB::raw('MAX(gpu_usage_percent) as gpu_max'),
                    DB::raw('COUNT(*) as sample_count')
                )
                ->groupBy('date')
                ->get();

            foreach ($metrics as $metric) {
                AgentMetricDaily::updateOrCreate(
                    [
                        'agent_id' => $agent->id,
                        'date' => $metric->date,
                    ],
                    [
                        'cpu_avg' => $metric->cpu_avg,
                        'cpu_min' => $metric->cpu_min,
                        'cpu_max' => $metric->cpu_max,
                        'ram_avg' => $metric->ram_avg,
                        'ram_min' => $metric->ram_min,
                        'ram_max' => $metric->ram_max,
                        'gpu_avg' => $metric->gpu_avg,
                        'gpu_min' => $metric->gpu_min,
                        'gpu_max' => $metric->gpu_max,
                        'sample_count' => $metric->sample_count,
                    ]
                );
            }

            $this->info("Agent {$agent->hostname}: agregováno " . $metrics->count() . " dní");
        }

        $this->info('Denní agregace dokončeny.');
    }
}
