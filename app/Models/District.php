<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class District extends Model
{
    use HasFactory;
    protected $fillable = [
        'district_code',
        'district_name',
    ];

    public function hospitals()
    {
        return $this->hasMany(Hospital::class, 'district_id', 'id');
    }
}
