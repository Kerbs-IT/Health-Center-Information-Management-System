<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class patients extends Model
{
    //
    protected $primaryKey = 'id'; // if the column is not the same like in 'user' table id and 'staff' user_id
    protected $keyType = 'int';
    protected $fillable = [
        'user_id',
        'patient_type',
        'first_name',
        'middle_initial',
        'last_name',
        'full_name',
        'age',
        'date_of_birth',
        'sex',
        'address_id',
        'civil_status',
        'contact_number',
        'nationality',
        'profile_image',
        'date_of_registration',
        'place_of_birth',
        'status'

    ];
    protected $casts = [
        'date_of_birth' => 'date',
        'date_of_registration' => 'date'
    ];

    public function address()
    {
        return $this->hasOne(patient_addresses::class, 'patient_id');
    }
    public function medical_record_case(){
        return $this->hasMany(medical_record_cases::class,'patient_id','id');
    }
    public function vaccination_masterlist(){
        return $this->hasOne(vaccination_masterlists::class,'patient_id','id');
    }
    //wra masterlist
    public function wra_masterlist(){
        return $this->hasOne(wra_masterlists::class, 'patient_id', 'id');
    }

    // louie's changes
    public function user(){
        return $this->belongsTo(User::class, 'user_id');
    }

    public function medicineRequests(){
        return $this->hasMany(MedicineRequest::class, 'patient_id');
    }


}
