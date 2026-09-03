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
        Schema::create('lead_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lead_id')->constrained('leads')->onDelete('cascade');
            $table->string('image_path')->nullable();
            $table->string('image_name');
            $table->string('image_caption')->nullable();
            $table->text('image_url')->nullable();
            $table->string('source')->nullable();
            $table->string('uploaded_by')->nullable()->comment('customer, staff');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lead_images');
    }
};
