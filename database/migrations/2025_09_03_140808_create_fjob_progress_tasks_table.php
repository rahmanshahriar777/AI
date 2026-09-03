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
        Schema::create('fjob_progress_tasks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('fjob_progress_id');
            $table->string('task_name');
            $table->text('description')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->enum('status', ['not_started', 'in_progress', 'completed', 'on_hold'])->default('not_started');
            $table->integer('progress_percent')->default(0); // task-level %
            $table->unsignedBigInteger('assigned_to')->nullable(); // FK to users/employees
            $table->unsignedBigInteger('parent_task_id')->nullable(); // for subtasks
            $table->unsignedBigInteger('dependency_task_id')->nullable(); // Gantt chart dependencies
            $table->integer('duration')->nullable(); // in days (optional)
            $table->decimal('planned_hours', 8, 2)->nullable(); // planned work
            $table->decimal('actual_hours', 8, 2)->nullable(); // tracked work
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fjob_progress_tasks');
    }
};
