<?php

use App\Models\Agent;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('agent_metrics_hourly', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Agent::class)
                ->constrained()
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->timestamp('hour_start'); // začátek hodiny

            // CPU statistiky
            $table->decimal('cpu_avg', 5, 2);
            $table->decimal('cpu_min', 5, 2);
            $table->decimal('cpu_max', 5, 2);

            // RAM statistiky
            $table->decimal('ram_avg', 5, 2);
            $table->decimal('ram_min', 5, 2);
            $table->decimal('ram_max', 5, 2);

            // GPU statistiky
            $table->decimal('gpu_avg', 5, 2)->nullable();
            $table->decimal('gpu_min', 5, 2)->nullable();
            $table->decimal('gpu_max', 5, 2)->nullable();

            $table->integer('sample_count'); // počet vzorků v hodině
            $table->timestamps();

            // Unikátní kombinace agent + hodina
            $table->unique(['agent_id', 'hour_start'], 'idx_agent_hour_unique');

            // Index pro časové dotazy
            $table->index(['agent_id', 'hour_start'], 'idx_agent_hour');
            $table->index('hour_start', 'idx_hour_start');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('agent_metric_hourlies');
    }
};
