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
        Schema::create('job_schedule_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_schedule_id')->constrained('job_schedules')->onDelete('cascade');
            $table->string('attachment_name');
            $table->string('attachment_path')->nullable();
            $table->string('attachment_type')->nullable();
            $table->string('status')->default('active')->comment('active, inactive');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_schedule_attachments');
    }
};
