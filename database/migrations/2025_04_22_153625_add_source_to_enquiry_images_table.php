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
        Schema::table('enquiry_images', function (Blueprint $table) {
            $table->text('image_url')->nullable();
            $table->string('source', 100)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('enquiry_images', function (Blueprint $table) {
            $table->dropColumn('image_url');
            $table->dropColumn('source');
        });
    }
};
