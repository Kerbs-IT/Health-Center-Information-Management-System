<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class senior_citizen_medical_records extends Model
{
    
    protected $fillable = [
        'medical_record_case_id',
        'health_worker_id',
        'patient_name',
        'occupation',
        'religion',
        'SSS',
        'blood_pressure',
        'temperature',
        'pulse_rate',
        'respiratory_rate',
        'height',
        'weight',
        'type_of_record'
    ];

    public function medical_record_case(){
        return $this->belongsTo(medical_record_cases::class, 'medical_record_case_id', 'id');
    }
    public function health_worker()
    {
        return $this->belongsTo(staff::class, 'health_worker_id', 'user_id');
    }
}
