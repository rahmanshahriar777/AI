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
        Schema::create('fjob_hs_check_details', function (Blueprint $table) {
            $table->id();
            $table->integer('fjob_hs_check_id');
            $table->integer('hs_checklist_id');
            $table->string('hs_checklist_title')->nullable();
            $table->integer('value')->default(0);
            $table->text('remarks')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fjob_hs_check_details');
    }
};
