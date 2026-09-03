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
        Schema::create('fake_job_schedule_workers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fake_job_schedule_id')->constrained('fake_job_schedules')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->date('assigned_date')->nullable();
            $table->date('completion_date')->nullable();
            $table->string('status')->default('pending')->comment('pending, inprogress, completed, cancelled');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fake_job_schedule_workers');
    }
};
