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
        Schema::table('enquiries', function (Blueprint $table) {
            $table->boolean('annual_maintenance')->default(false);
            $table->boolean('installations')->default(false);
            $table->boolean('repairs')->default(false);
            $table->boolean('testing')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('enquiries', function (Blueprint $table) {
            $table->dropColumn([
                'annual_maintenance',
                'installations',
                'repairs',
                'testing',
            ]);
        });
    }
};
