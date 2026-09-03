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
        Schema::create('vehicle_overview_details', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('vehicle_overview_id')->unsigned();
            $table->bigInteger('vehicle_check_categories_id')->unsigned();
            $table->bigInteger('vehicle_check_checklists_id')->unsigned();
            $table->boolean('has_attribute')->default(false);
            $table->bigInteger('vehicle_check_checklist_attributes_id')->nullable();
            $table->string('overview_value')->nullable()->comment('Ok, NotOk, N/A');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicle_overview_details');
    }
};
