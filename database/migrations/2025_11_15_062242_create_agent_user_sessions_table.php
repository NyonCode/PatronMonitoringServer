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
        Schema::create('agent_user_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Agent::class)
                ->constrained()
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->string('session_user')->nullable()->comment('Session user');
            $table->timestamp('session_start')->nullable()->comment('Session start');
            $table->json('mapped_drivers')->nullable()->comment('Mapped network disks');
            $table->json('accessible_paths')->nullable()->comment('Accessible paths');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('agent_user_sessions', function (Blueprint $table) {
            $table->dropForeign(['agent_id']);
        });

        Schema::dropIfExists('agent_user_sessions');
    }
};
