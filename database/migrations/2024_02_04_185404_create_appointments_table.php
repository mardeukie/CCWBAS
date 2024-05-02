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
        Schema::create('appointments', function (Blueprint $table) {
            $table->BigInteger('id');
            $table->string('type');
            $table->enum('status', ['completed', 'cancelled', 'no show', 'booked'])->default('booked');
            $table->BigInteger('patient_id');
            $table->BigInteger('slot_id');
            $table->foreign('patient_id')->references('id')->on('patients');
            $table->foreign('slot_id')->references('id')->on('slots');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
