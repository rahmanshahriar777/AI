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
        Schema::create('vehicle_details', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('category_id')->unsigned()->nullable(); // Foreign key to vehicle_category table
            $table->string('registration_no')->unique(); // UK vehicle reg number (AB12 CDE)
            $table->string('serial_no')->nullable(); // Vehicle number plate
            $table->string('vin_number')->unique(); // Vehicle Identification Number
            $table->string('mechanical_code')->nullable(); // Mechanical code for the vehicle
            $table->string('electronic_code')->nullable();
            $table->string('radio_code')->nullable();
            $table->string('deadlock_key_duplication_codes')->nullable(); // Key code for the vehicle
            $table->string('manufacturer')->nullable(); // Ford, BMW
            $table->string('model')->nullable(); // Transit, X5
            $table->string('engine')->nullable(); // Engine type or model
            $table->string('color')->nullable(); // Vehicle color
            $table->integer('year')->nullable();
            $table->string('fuel_type')->nullable(); // Petrol, Diesel, Electric, Hybrid
            $table->integer('mileage')->default(0);
            $table->string('tyre_size_front')->nullable(); // Tyre size for front wheels
            $table->string('tyre_size_rear')->nullable();

            // UK Compliance
            $table->date('mot_expiry_date')->nullable();
            $table->date('tax_expiry_date')->nullable();
            $table->date('insurance_expiry_date')->nullable();
            $table->date('loler_expire_date')->nullable();

            // Service Records
            $table->date('last_service_date')->nullable();
            $table->date('next_service_date')->nullable();

            // Status
            $table->enum('status', ['Available', 'Assigned', 'Under Maintenance', 'Inactive'])
                ->default('Available');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicle_details');
    }
};
