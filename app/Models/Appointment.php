<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    use HasFactory;
    protected $fillable = [
        'type',
        'status',
        'patient_id',
        'slot_id',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function slot()
    {
        return $this->belongsTo(Slot::class);
    }

    public function complaints()
    {
        return $this->hasMany(Complaint::class);
    }

    public function records()
    {
        return $this->hasMany(Record::class);
    }

    public function smsTrackings()
    {
        return $this->hasMany(SmsTracking::class);
    }

}
