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
        Schema::create('fjob_attribute_details', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('fjob_attribute_id')->unsigned();
            $table->bigInteger('job_attribute_detail_id')->unsigned();
            $table->string('detail_value')->nullable();
            $table->string('remarks')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fjob_attribute_details');
    }
};
