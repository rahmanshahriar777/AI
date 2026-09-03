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
        Schema::create('stock_item_requisition_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stock_item_requisition_id')->constrained('stock_item_requisitions')->onDelete('cascade');
            $table->foreignId('stock_item_variant_id')->constrained('stock_item_variants')->onDelete('cascade');
            $table->decimal('quantity', 10, 2);
            $table->string('unit', 100)->default('pcs'); // Unit of measurement for the requisition detail
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_item_requisition_details');
    }
};
