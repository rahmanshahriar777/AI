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
        Schema::create('fjob_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fjob_id')->constrained('fjobs')->onDelete('cascade');
            $table->string('attachment_name');
            $table->text('description')->nullable();
            $table->string('attachment_path')->nullable();
            $table->text('attachment_url')->nullable();
            $table->string('attachment_type')->default('doc')->comment('doc,pdf')->nullable();
            $table->string('source')->nullable();
            $table->string('status')->default('active')->comment('active, inactive');
            $table->string('uploaded_by')->nullable()->comment('customer, staff');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fjob_attachments');
    }
};
