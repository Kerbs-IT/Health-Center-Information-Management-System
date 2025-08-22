<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class prenatal_medical_records extends Model
{
    //
    protected $fillable = [
        'medical_record_case_id',
        'family_head_name',
        'blood_type',
        'religion',
        'philHealth_number',
        'family_serial_no',
        'family_planning_decision',
        'health_worker_id',
        'type_of_record'

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
