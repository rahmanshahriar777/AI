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
        Schema::create('quotation_template_section_attachments', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('quotation_template_section_id');
            $table->string('attachment_name');
            $table->string('attachment_path')->nullable();
            $table->string('attachment_type')->nullable();
            $table->string('status')->default('active')->comment('active, inactive');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quotation_template_section_attachments');
    }
};
