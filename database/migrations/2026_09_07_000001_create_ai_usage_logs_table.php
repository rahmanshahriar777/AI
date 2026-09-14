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
        Schema::create('ai_usage_logs', function (Blueprint $table) {
            $table->id();
            $table->string('provider', 32);
            $table->string('model', 64)->nullable();
            $table->unsignedInteger('prompt_tokens')->nullable();
            $table->unsignedInteger('completion_tokens')->nullable();
            $table->unsignedInteger('total_tokens')->nullable();
            $table->float('latency_ms')->default(0);
            $table->string('status', 16); // 'success', 'failed', 'cap_skipped'
            $table->string('error_type', 32)->nullable(); // 'rate_limit', 'timeout', 'authentication', 'cap_exceeded', 'generic'
            $table->text('error_message')->nullable();
            $table->string('module', 64)->nullable();
            $table->timestamps();

            $table->index(['provider', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_usage_logs');
    }
};
