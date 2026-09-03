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
        Schema::create('quotation_section_attachments', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('quotation_section_id');
            $table->string('attachment_name');
            $table->string('attachment_path')->nullable();
            $table->string('attachment_url')->nullable();
            $table->string('source')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quotation_section_attachments');
    }
};
