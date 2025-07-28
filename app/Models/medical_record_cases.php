<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class medical_record_cases extends Model
{
    //
    protected $fillable = [
        'patient_id',
        'type_of_case'
    ];

    public function patient(){
        return $this-> belongsTo(patients::class,'patient_id','id');
    }
    public function vaccination_medical_record(){
        return $this-> hasOne(vaccination_medical_records::class,'medical_record_case_id','id');
    }
    public function vaccination_case_record(){
        return $this->hasMany(vaccination_case_records::class, 'medical_record_case_id','id');
    }
}
