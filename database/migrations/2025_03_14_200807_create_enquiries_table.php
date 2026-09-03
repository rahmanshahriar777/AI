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
        Schema::create('enquiries', function (Blueprint $table) {
            $table->id();
            $table->string('enquiry_name')->unique()->comment('20250604-0001');
            $table->string('slug')->unique()->comment('Unique slug for the enquiry');
            $table->text('enquiry')->nullable();
            $table->text('enquiry_description')->nullable();
            $table->foreignId('customer_id')->nullable()->constrained('customers')->onDelete('cascade');
            $table->string('enquiry_type')->default('general')->comment('general, sales, support, technical');
            $table->string('enquiry_category')->nullable()->comment('complaint, query, request, feedback');
            $table->string('enquiry_priority')->default('low')->comment('low, medium, high');
            $table->string('enquiry_source')->nullable()->comment('phone, email, chat, social_media');
            $table->string('enquiry_status')->default('new')->comment('new, inprogress, on_hold, converted_to_lead, rejected, archived');
            $table->string('enquiry_closed_by')->nullable();
            $table->dateTime('enquiry_closed_at')->nullable();
            $table->text('enquiry_close_reason')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('enquiries');
    }
};
