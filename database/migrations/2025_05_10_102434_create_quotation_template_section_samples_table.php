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
        Schema::create('quotation_template_section_samples', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quotation_template_section_id')->constrained('quotation_template_sections')->onDelete('cascade');
            $table->string('sample_type', 100)->comment("('text', 'image', 'video', 'file')")->default('text');
            $table->text('sample_source')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quotation_template_section_samples');
    }
};
