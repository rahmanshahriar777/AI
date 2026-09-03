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
        Schema::create('hs_checklist_attributes', function (Blueprint $table) {
            $table->id();
            $table->integer('hs_checklist_id');
            $table->string('title');
            $table->tinyText('short_details')->nullable();
            $table->boolean('status')->default('true');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hs_checklist_attributes');
    }
};
