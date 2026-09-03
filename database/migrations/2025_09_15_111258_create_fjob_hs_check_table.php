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
        Schema::create('fjob_hs_check', function (Blueprint $table) {
            $table->id();
            $table->integer('fjob_id');
            $table->string('job_title')->nullable();
            $table->string('hs_check_title')->nullable();
            $table->integer('checked_by_user')->nullable();
            $table->date('check_date')->nullable();
            $table->text('remarks')->nullable();
            $table->enum('status', ['pending', 'in_progress', 'completed', 'rejected'])->default('completed');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fjob_hs_check');
    }
};
