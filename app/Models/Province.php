<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Province extends Model
{
    use HasFactory;
    protected $table = 'province';
    protected $fillable = ['province']; // Add other columns as needed

    public function municipalities()
    {
        return $this->hasMany(Municipality::class);
    }

    public function patients()
    {
        return $this->hasMany(Patient::class);
    }
}
