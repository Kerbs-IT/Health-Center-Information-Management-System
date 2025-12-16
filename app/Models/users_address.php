<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class users_address extends Model
{
    //
    protected $fillable = [
        'patient_id',
        'user_id',
        'house_number',
        'street',
        'purok',
        'barangay',
        'city',
        'province',
        'postal_code',
        'latitude',
        'longitude',
    ];

    public function user(){
        return $this->belongsTo(User::class,'user_id','id');
    }
}
