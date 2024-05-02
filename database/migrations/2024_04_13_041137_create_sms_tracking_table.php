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
        Schema::create('sms_tracking', function (Blueprint $table) {
            $table->id();
            $table->enum('direction', ['inbound', 'outbound']);
            $table->string('status', 20)->nullable();
            $table->dateTime('date_sent')->nullable();
            $table->string('message_sid')->nullable(); // Twilio's message SID
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sms_tracking');
    }
};
