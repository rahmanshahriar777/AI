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
        Schema::create('vehicle_overviews', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('vehicle_id')->unsigned();
            $table->date('overview_date');
            $table->float('mileage')->default('0.00');
            $table->date('next_overview_date')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicle_overviews');
    }
};
