<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class patient_addresses extends Model
{
    protected $primaryKey = 'id'; // if the column is not the same like in 'user' table id and 'nurse' user_id
    protected $keyType = 'int';
    protected $fillable =[
        'patient_id',
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

    public function patient(){
        return $this -> belongsTo(patients::class);
    }

    public function vaccination_masterlist(){
        return $this->belongsTo(vaccination_masterlists::class,'address_id','id');
    }
    // wra masterlist
    public function wra_masterlist(){
        return $this->belongsTo(wra_masterlists::class, 'address_id', 'id');
    }
}
