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
        Schema::create('hs_tool_checkups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hs_tool_id')->constrained('hs_tools')->onDelete('cascade');
            $table->string('tool_name');
            $table->string('checkup_name');
            $table->date('checkup_date');
            $table->string('performed_by')->nullable();
            $table->text('remarks')->nullable();
            $table->enum('status', ['pending', 'in_progress', 'completed', 'rejected'])->default('completed');
            $table->date('next_checkup_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hs_tool_checkups');
    }
};
