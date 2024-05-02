<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Slot extends Model
{
    use HasFactory;
    protected $fillable = [
        'start_time',
        'end_time',
        'status',
        'medstaff_id',
        'booking_limit_id',
    ];

    public function medstaff()
    {
        return $this->belongsTo(Medstaff::class);
    }

    public function bookingLimit()
    {
        return $this->belongsTo(BookingLimit::class, 'booking_limit_id');
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }
}
