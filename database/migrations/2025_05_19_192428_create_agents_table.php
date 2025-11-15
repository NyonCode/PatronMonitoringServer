<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('agents', function (Blueprint $table) {
            $table->id();
            $table->string('uuid')->unique()->nullable(false)->index();
            $table->string('hostname')->nullable(false);
            $table->string('ip_address')->nullable(true);
            $table->string('pretty_name')->nullable(true);
            $table->integer('update_interval')->nullable(false)->default(60);
            $table->dateTime('last_seen_at')->index();
            $table->string('token');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('agents');
    }
};
