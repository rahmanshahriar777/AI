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
        Schema::create('fjob_quotations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fjob_id')->constrained('fjobs')->onDelete('cascade');
            $table->bigInteger('quotation_id');
            $table->string('quotation_number')->nullable();
            $table->date('quotation_date')->nullable();
            $table->date('valid_until')->nullable();
            $table->decimal('total_amount', 15, 2)->nullable();
            $table->string('status')->default('approved'); // e.g., pending, approved, rejected
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fjob_quotations');
    }
};
