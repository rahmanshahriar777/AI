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
        Schema::create('driver_details', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->unsigned()->nullable();
            $table->string('license_no')->unique(); // UK Driving Licence Number
            $table->date('license_issue_date')->nullable();
            $table->date('license_expiry')->nullable();
            $table->string('license_category')->nullable(); // e.g. B, C1, D1, HGV
            $table->boolean('digital_tacho_card')->default(false); // Tachograph card for lorry/bus drivers
            $table->boolean('dbs_check_passed')->default(false); // Disclosure & Barring Service
            $table->boolean('medical_check_passed')->default(false);
            $table->date('last_medical_check_date')->nullable();
            $table->date('next_medical_due_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('driver_details');
    }
};
