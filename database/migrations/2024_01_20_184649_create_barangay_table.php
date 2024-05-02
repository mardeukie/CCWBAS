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
        Schema::create('barangay', function (Blueprint $table) {
            $table->id();
            $table->string('barangay');
            $table->UnsignedBigInteger('municipality_id');
            $table->timestamps();

            //foreign id
            $table->foreign('municipality_id')->references('id')->on('municipality');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('barangay');
    }
};
