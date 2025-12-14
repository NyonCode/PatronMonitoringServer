<?php

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
        Schema::create('terminal_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('terminal_session_id')->constrained('terminal_sessions')->cascadeOnDelete();
            $table->string('direction', 10);
            $table->longText('content');
            $table->timestamps();

            $table->index(['terminal_session_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('terminal_logs');
    }
};
