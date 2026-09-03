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
        Schema::create('vehicle_check_checklist_attributes', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('vehicle_check_checklist_id')->unsigned();
            $table->string('attribute_name');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicle_check_checklist_attributes');
    }
};
