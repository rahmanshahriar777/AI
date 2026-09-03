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
        Schema::create('fjob_notes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fjob_id')->constrained('fjobs')->onDelete('cascade');
            $table->text('note');
            $table->string('note_by')->nullable()->comment('customer, staff');
            $table->string('note_type')->default('general')->comment('general, internal, external');
            $table->string('note_status')->default('active')->comment('active, inactive');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fjob_notes');
    }
};
