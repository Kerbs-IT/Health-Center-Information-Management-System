<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class vaccination_case_records extends Model
{
    //
    protected $fillable =[
        'medical_record_case_id',
        'patient_name',
        'administered_by',
        'date_of_vaccination',
        'time',
        'vaccine_type',
        'dose_number',
        'remarks',
        'type_of_record'

    ];

    public function medical_case_record(){
        return $this->belongsTo(medical_record_cases::class,'medical_record_case_id','id');
    }
}
