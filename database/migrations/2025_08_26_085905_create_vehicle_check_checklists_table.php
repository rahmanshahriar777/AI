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
        Schema::create('vehicle_check_checklists', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('vehicle_check_category_id')->unsigned();
            $table->string('title');
            $table->boolean('has_attributes')->default(false);
            $table->boolean('status')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicle_check_checklists');
    }
};
