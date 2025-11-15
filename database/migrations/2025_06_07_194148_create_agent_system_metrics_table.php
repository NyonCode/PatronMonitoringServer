<?php

use App\Models\Agent;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('agent_system_metrics', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Agent::class)
                ->constrained()
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->timestamp('recorded_at');
            $table->decimal('cpu_usage_percent', 5, 2);
            $table->decimal('ram_usage_percent', 5, 2);
            $table->decimal('gpu_usage_percent', 5, 2)->nullable();
            $table->timestamps();

            // Composite index pro časové dotazy na konkrétního agenta
            // Nejčastější dotaz: SELECT * FROM metrics WHERE agent_id = ? AND recorded_at > ? ORDER BY recorded_at
            $table->index(['agent_id', 'recorded_at'], 'idx_agent_time');

            // Index pro čištění starých dat
            $table->index('recorded_at', 'idx_recorded_at');

            // Composite index pro agregace podle času
            $table->index(['recorded_at', 'agent_id'], 'idx_time_agent');
        });

        // Pro MySQL/MariaDB - optimalizace pro časové řady
        if (DB::getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE agent_system_metrics ROW_FORMAT=COMPRESSED');
        }
    }

    public function down(): void
    {
        Schema::table('agent_system_metrics', function (Blueprint $table) {
            $table->dropForeign(['agent_id']);
        });

        Schema::dropIfExists('agent_system_metrics');
    }
};
