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
        Schema::create('stock_item_movements', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('item_variant_id');
            $table->foreign('item_variant_id')->references('id')->on('stock_item_variants')->onDelete('cascade');
            $table->float('quantity');
            $table->string('unit', 100)->default('pcs'); // Unit of measurement for the movement
            $table->decimal('cost', 10, 2)->nullable(); // Cost of the item variant at the time of movement
            $table->enum('movement_type', ['in', 'out', 'transfer'])->default('in'); // Type of movement: in, out, or adjustment
            $table->timestamp('movement_date')->useCurrent();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            $table->string('reference')->nullable(); // e.g., purchase order number, sales order number, etc.
            $table->unsignedBigInteger('source_warehouse_id')->nullable();
            $table->foreign('source_warehouse_id')->references('id')->on('stock_warehouses')->onDelete('set null');
            $table->unsignedBigInteger('destination_warehouse_id')->nullable();
            $table->foreign('destination_warehouse_id')->references('id')->on('stock_warehouses')->onDelete('set null');
            $table->string('source')->nullable(); // e.g., 'warehouse', 'store', etc.
            $table->string('destination')->nullable(); // e.g., 'warehouse', 'store', etc.
            $table->string('status')->default('pending'); // e.g., 'pending', 'completed', 'cancelled'
            $table->string('reason')->nullable(); // Reason for the movement, if applicable
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_item_movements');
    }
};
