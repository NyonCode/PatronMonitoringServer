<?php

use App\Models\Agent;
use App\Models\User;
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
        Schema::create('terminal_sessions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignIdFor(Agent::class)->constrained()->cascadeOnDelete();
            $table->string('type', 20)->default('powershell');
            $table->integer('user_session_id')->nullable();
            $table->string('status', 20)->default('running');
            $table->timestamp('started_at')->nullable();
            $table->timestamp('closed_at')->nullable();
            $table->foreignIdFor(User::class, 'created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['agent_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('terminal_sessions');
    }
};
