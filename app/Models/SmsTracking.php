<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SmsTracking extends Model
{
    use HasFactory;

    protected $table = 'sms_tracking';
    protected $fillable = ['direction', 'status', 'date_sent', 'message_sid'];

    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }
}
