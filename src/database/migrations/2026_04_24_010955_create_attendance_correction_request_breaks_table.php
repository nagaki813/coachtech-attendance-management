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
        Schema::create('attendance_correction_request_breaks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('correction_request_id');
            $table->foreign('correction_request_id', 'acr_breaks_request_id_foreign')
                ->references('id')
                ->on('attendance_correction_requests')
                ->cascadeOnDelete();
            $table->dateTime('break_start');
            $table->dateTime('break_end')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendance_correction_request_breaks');
    }
};
