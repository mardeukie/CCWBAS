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
        Schema::create('slots', function (Blueprint $table) {
            $table->BigInteger('id');
            $table->Time('start_time');
            $table->Time('end_time');
            $table->enum('status', ['available', 'fully booked', 'not applicable'])->default('available');
            $table->BigInteger('medstaff_id');
            $table->BigInteger('booking_limit_id');
            $table->timestamps();
            $table->foreign('medstaff_id')->references('id')->on('medstaffs');
            $table->foreign('booking_limit_id')->references('id')->on('booking_limits');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('slots');
    }
};
