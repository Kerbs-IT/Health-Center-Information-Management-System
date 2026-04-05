<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class gc_case_records extends Model
{
    //
    protected $fillable = [
        'medical_record_case_id',
        'health_worker_id',
        'blood_pressure',
        'temperature',
        'pulse_rate',
        'respiratory_rate',
        'height',
        'weight',
        'date_of_consultation',
        'symptoms',
        'diagnosis',
        'treatment_plan',
        'status',
        'type_of_record',
    ];


    public function medical_record_case()
    {
        return $this->belongsTo(medical_record_cases::class, 'medical_record_case_id', 'id');
    }
    public function health_worker()
    {
        return $this->belongsTo(staff::class, 'health_worker_id', 'user_id');
    }
}
