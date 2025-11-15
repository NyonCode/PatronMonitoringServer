<?php

use App\Models\Agent;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('agent_disks', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Agent::class)
                ->constrained()
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->string('name', 100);
            $table->decimal('usage_percent', 5, 2);
            $table->unsignedBigInteger('free'); // v bytech
            $table->unsignedBigInteger('size'); // v bytech
            $table->timestamps();

            // Jedinečný disk pro každého agenta
            $table->unique(['agent_id', 'name'], 'idx_agent_disk_unique');

            // Index pro rychlé vyhledání všech disků agenta
            $table->index('agent_id', 'idx_agent_id');

            // Index pro vyhledání kritických disků (vysoké využití)
            $table->index('usage_percent', 'idx_usage_percent');
        });
    }

    public function down(): void
    {
        Schema::table('agent_disks', function (Blueprint $table) {
            $table->dropForeign(['agent_id']);
        });

        Schema::dropIfExists('agent_disks');
    }
};
