<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class tb_dots_check_ups extends Model
{
    //
    protected $fillable = [
        'medical_record_case_id',
        'health_worker_id',
        'patient_name',
        'date_of_visit',
        'blood_pressure',
        'temperature',
        'pulse_rate',
        'respiratory_rate',
        'height',
        'weight',
        'adherence_of_treatment',
        'side_effect',
        'progress_note',
        'sputum_test_result',
        'treatment_phase',
        'outcome',
        'type_of_record',
        'status'
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
