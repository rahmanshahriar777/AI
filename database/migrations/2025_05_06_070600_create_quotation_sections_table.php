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
        Schema::create('quotation_sections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quotation_id')->constrained('quotations')->onDelete('cascade');
            $table->string('section_name');
            $table->string('section_slug')->unique()->comment('Unique slug for the quotation section');
            $table->string('section_type', 100);
            $table->boolean('is_required')->default(0);
            $table->boolean('has_attachments')->default(0);
            $table->string('attachment_type')->nullable();
            $table->integer('order')->default(0);
            $table->string('status')->default('active')->comment('active, inactive');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quotation_sections');
    }
};
