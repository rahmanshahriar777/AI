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
        Schema::create('quotation_template_sections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quotation_template_id')->constrained('quotation_templates')->onDelete('cascade');
            $table->string('section_name');
            $table->string('section_slug');
            $table->string('section_type', 100);
            $table->boolean('is_required')->default(0);
            $table->boolean('has_attachments')->default(0);
            $table->string('attachment_type')->nullable();
            $table->integer('order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quotation_template_sections');
    }
};
