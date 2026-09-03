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
        Schema::create('stock_item_variant_attributes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stock_item_variant_id')->constrained('stock_item_variants')->onDelete('cascade');
            $table->foreignId('stock_attribute_id')->constrained('stock_attributes')->onDelete('cascade');
            $table->float('value')->default(0); // Assuming the value is a float, adjust as necessary
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_item_variant_attributes');
    }
};
