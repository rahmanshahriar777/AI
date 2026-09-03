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
        Schema::create('quotations', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('lead_id');
            $table->bigInteger('customer_id');
            $table->bigInteger('job_type_id');
            $table->string('quotation_version', 200)->nullable();
            $table->string('quotation_date', 100)->nullable();
            $table->text('cover_letter')->nullable();
            $table->bigInteger('quotation_template_id');
            $table->date('valid_until')->nullable();
            $table->decimal('total_amount', 15, 2)->nullable();
            $table->string('status', 100)->comment('draft, accepted, declined')->default('draft');
            $table->text('remarks')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quotations');
    }
};
