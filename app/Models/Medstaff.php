<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Medstaff extends Model
{
    use HasFactory;
    protected $fillable = [
        'first_name',
        'middle_name',
        'last_name',
        'contact_number',
        'license_number',
        'user_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function slots()
    {
        return $this->hasMany(Slot::class);
    }
    public function records()
    {
        return $this->hasMany(Record::class);
    }
}
