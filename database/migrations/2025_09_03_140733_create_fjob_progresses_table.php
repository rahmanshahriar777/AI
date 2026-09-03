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
        Schema::create('fjob_progresses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('fjob_id');
            $table->string('job_title')->nullable();
            $table->unsignedBigInteger('customer_id')->nullable(); // use FK instead of text if possible
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->enum('status', ['not_started', 'in_progress', 'completed', 'on_hold'])->default('not_started');
            $table->integer('progress_percent')->default(0); // overall job progress %
            $table->text('priority')->nullable(); // low, medium, high
            $table->text('notes')->nullable(); // general remarks
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fjob_progresses');
    }
};
