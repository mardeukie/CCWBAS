<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Record extends Model
{
    use HasFactory;
    protected $fillable = [
        'patient_id',
        'doctor_id',
        'medstaff_id',
        'appointment_id',
        'vital_signs',
        'diagnosis',
        'treatments',
        'medications',
        'referral',
        'notes',
        'date'
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }

    public function medstaff()
    {
        return $this->belongsTo(Medstaff::class);
    }

    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }
}
