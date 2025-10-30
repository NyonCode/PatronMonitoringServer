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
            $table->timestamp('recorded_at')->index();
            $table->float('cpu_usage_percent')->index();
            $table->float('ram_usage_percent')->index();
            $table->float('gpu_usage_percent')->index();
            $table->foreignIdFor(Agent::class)
                ->constrained()
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('agent_system_metrics');
    }
};
