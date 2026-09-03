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
        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('enquiry_id');
            $table->text('enquiry')->nullable();
            $table->string('lead_name');
            $table->string('slug');
            $table->text('lead_title')->nullable();
            $table->text('lead_description')->nullable();
            $table->foreignId('customer_id')->nullable()->constrained('customers')->onDelete('cascade');
            $table->string('lead_type')->default('general')->comment('general, sales, support, technical');
            $table->string('lead_category')->nullable()->comment('complaint, query, request, feedback');
            $table->string('lead_priority')->default('low')->comment('low, medium, high');
            $table->string('lead_status')->default('new')->comment('new, inprogress, on_hold, converted_to_job, rejected, archived');
            $table->string('lead_closed_by')->nullable();
            $table->dateTime('lead_closed_at')->nullable();
            $table->text('lead_close_reason')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leads');
    }
};
