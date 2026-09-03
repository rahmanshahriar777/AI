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
        Schema::create('fjob_attributes', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('fjob_id')->unsigned();
            $table->bigInteger('job_attribute_id')->unsigned();
            $table->string('attribute_value')->nullable();
            $table->string('remarks')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fjob_attributes');
    }
};
