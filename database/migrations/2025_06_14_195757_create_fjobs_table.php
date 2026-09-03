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
        Schema::create('fjobs', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('enquiry_id');
            $table->text('enquiry')->nullable();
            $table->bigInteger('lead_id');
            $table->string('job_name');
            $table->string('slug');
            $table->text('job_title')->nullable();
            $table->text('job_description')->nullable();
            $table->foreignId('customer_id')->nullable()->constrained('customers')->onDelete('cascade');
            $table->string('job_type')->nullable();
            $table->string('job_category')->nullable()->comment('complaint, query, request, feedback');
            $table->string('job_priority')->default('low')->comment('low, medium, high');
            $table->string('job_status')->default('new')->comment('new, inprogress, on_hold, converted_to_job, rejected, completed, archived');
            $table->string('job_closed_by')->nullable();
            $table->dateTime('job_closed_at')->nullable();
            $table->text('job_close_reason')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fjobs');
    }
};
