<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookingLimit extends Model
{
    use HasFactory;
    protected $fillable = [
        'date',
        'slot_number',
    ];

    public function slots()
    {
        return $this->hasMany(Slot::class, 'booking_limit_id');
    }

}
