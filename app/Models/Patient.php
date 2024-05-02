<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

use Illuminate\Database\Eloquent\SoftDeletes;

class Patient extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'first_name',
        'middle_name',
        'last_name',
        'contact_number',
        'gender',
        'date_of_birth',
        'status',
        'province_id',
        'municipality_id',
        'barangay_id',
        'user_id',
    ];
    protected $dates = ['date_of_birth'];

    public function getDateOfBirthAttribute($value)
    {
        return Carbon::parse($value);
    }

    public function getAgeAttribute()
    {
        return $this->date_of_birth->age;
    }

    public function province()
    {
        return $this->belongsTo(Province::class, 'province_id');
    }
    
    public function municipality()
    {
        return $this->belongsTo(Municipality::class, 'municipality_id');
    }
    
    public function barangay()
    {
        return $this->belongsTo(Barangay::class, 'barangay_id');
    }
    
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    public function slots()
    {
        return $this->hasManyThrough(Slot::class, Appointment::class);
    }

    public function complaints()
    {
        return $this->hasMany(Complaint::class);
    }

    public function records()
    {
        return $this->hasMany(Record::class);
    }
    
}
