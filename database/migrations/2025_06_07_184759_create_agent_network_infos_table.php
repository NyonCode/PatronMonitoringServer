<?php

use App\Models\Agent;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('agent_network_infos', function (Blueprint $table) {
            $table->id();
            $table->string('ip_address')->nullable();
            $table->string('subnet_mask')->nullable();
            $table->string('gateway')->nullable();
            $table->json('dns')->nullable();
            $table->string('mac_address')->nullable();
            $table->foreignIdFor(Agent::class)
                ->constrained()
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::table('agent_network_infos', function (Blueprint $table) {
            $table->dropForeign(['agent_id']);
        });

        Schema::dropIfExists('agent_network_infos');
    }
};
