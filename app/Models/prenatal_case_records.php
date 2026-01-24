<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class prenatal_case_records extends Model
{
    protected $fillable = [
        'medical_record_case_id',
        'patient_name',
        'G',
        'P',
        'T',
        'premature',
        'abortion',
        'living_children',
        'LMP',
        'expected_delivery',
        'menarche',
        'tetanus_toxoid_1',
        'tetanus_toxoid_2',
        'tetanus_toxoid_3',
        'tetanus_toxoid_4',
        'tetanus_toxoid_5',
        'decision',
        'type_of_record',
        'health_worker_id',
        'blood_pressure',
        'temperature',
        'pulse_rate',
        'respiratory_rate',
        'height',
        'weight',
        'status',
        'planning'

    ];
    public function medical_record_case()
    {
        return $this->belongsTo(medical_record_cases::class, 'medical_record_case_id', 'id');
    }
    public function health_worker()
    {
        return $this->belongsTo(staff::class, 'health_worker_id', 'user_id');
    }
    public function pregnancy_timeline_records(){
        return $this-> hasMany(pregnancy_timeline_records::class, 'prenatal_case_record_id','id');
    }

    public function prenatal_assessment(){
        return $this-> hasOne(prenatal_assessments::class, "prenatal_case_record_id", "id");
    }

    public function pregnancy_history_questions(){
        return $this->hasMany(pregnancy_history_questions::class, "prenatal_case_record_id", "id");
    }

}
