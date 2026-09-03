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
        Schema::create('lead_addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lead_id')->constrained('leads')->onDelete('cascade');
            $table->string('contact_firstname');
            $table->string('contact_lastname')->nullable();
            $table->string('contact_phone', 20)->nullable();
            $table->string('contact_mobile', 20)->nullable();
            $table->string('contact_email', 100)->nullable();
            $table->text('address')->nullable();
            $table->string('county', 100)->nullable();
            $table->string('postcode', 50)->nullable();
            $table->string('country', 100)->nullable();
            $table->text('location')->nullable();
            $table->string('status')->default('active')->comment('active, inactive');
            $table->string('default')->default('no')->comment('yes, no');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lead_addresses');
    }
};
