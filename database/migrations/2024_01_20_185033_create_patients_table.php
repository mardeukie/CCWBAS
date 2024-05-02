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
        Schema::create('patients', function (Blueprint $table) {
            $table->BigInteger('id');
            $table->string('first_name');
            $table->string('middle_name');
            $table->string('last_name');
            $table->string('contact_number');
            $table->string('gender');
            $table->date('date_of_birth');
            $table->string('status');

            //foreign keys
            $table->foreignId('province_id')->nullable()->constrained('province');
            $table->foreignId('municipality_id')->nullable()->constrained('municipality');
            $table->foreignId('barangay_id')->nullable()->constrained('barangay');

            $table->foreignId('user_id')->nullable()->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patients');
    }
};
