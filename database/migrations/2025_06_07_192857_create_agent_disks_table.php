<?php

use App\Models\Agent;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('agent_disks', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->float('usage_percent');
            $table->string('free');
            $table->string('size');
            $table->foreignIdFor(Agent::class)
                ->constrained()
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->unique(['agent_id', 'name']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('agent_disks');
    }
};
