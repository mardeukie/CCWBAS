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
        Schema::create('records', function (Blueprint $table) {
            $table->BigInteger('id');
            $table->BigInteger('patient_id');
            $table->BigInteger('doctor_id');
            $table->BigInteger('medstaff_id')->nullable();
            $table->BigInteger('appointment_id')->nullable();
            $table->json('vital_signs')->nullable();
            $table->text('diagnosis')->nullable();
            $table->text('treatments')->nullable();
            $table->text('medications')->nullable();
            $table->text('referral')->nullable();
            $table->text('notes')->nullable();
            $table->date('date');
            $table->timestamps();

            $table->foreign('patient_id')->references('id')->on('patients')->onDelete('cascade');
            $table->foreign('doctor_id')->references('id')->on('doctors')->onDelete('cascade');
            $table->foreign('medstaff_id')->references('id')->on('medstaffs')->onDelete('set null');
            $table->foreign('appointment_id')->references('id')->on('appointments')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('records');
    }
};
