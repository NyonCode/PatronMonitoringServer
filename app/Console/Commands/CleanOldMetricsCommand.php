<?php

namespace App\Console\Commands;

use App\Models\AgentSystemMetric;
use Illuminate\Console\Command;

class CleanOldMetricsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'metrics:clean {--days=7 : Počet dní surových metrik k zachování}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Maže staré surové metriky (agregované data zůstávají zachována)';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $days = (int) $this->option('days');

        $this->info("Mažu surové metriky starší než {$days} dní...");

        $deleted = AgentSystemMetric::where('recorded_at', '<', now()->subDays($days))
            ->delete();

        $this->info("Smazáno {$deleted} záznamů.");
    }
}
