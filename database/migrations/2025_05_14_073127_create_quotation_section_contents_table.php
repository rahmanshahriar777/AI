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
        Schema::create('quotation_section_contents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quotation_section_id')->constrained('quotation_sections')->onDelete('cascade');
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
        Schema::dropIfExists('quotation_section_contents');
    }
};
