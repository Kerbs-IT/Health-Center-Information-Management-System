<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class pregnancy_checkups extends Model
{
    protected $fillable = [
        'medical_record_case_id',
        'patient_name',
        'health_worker_id',
        "check_up_time",
        "check_up_blood_pressure",
        "check_up_temperature",
        "check_up_pulse_rate" .
            "check_up_respiratory_rate",
        "check_up_height",
        "check_up_weight",
        "abdomen_question",
        "abdomen_question_remarks",
        "vaginal_question",
        "vaginal_question_remarks",
        "headache_question",
        "headache_question_remarks",
        "swelling_question",
        "swelling_question_remarks",
        "blurry_vission_question",
        "blurry_vission_question_remarks",
        "urination_question",
        "urination_question_remarks",
        "baby_move_question",
        "baby_move_question_remarks",
        "decreased_baby_movement",
        "decreased_baby_movement_remarks",
        "other_symptoms_question",
        "other_symptoms_question_remarks",
        "overall_remarks",
        'status',
        'date_of_comeback'
    ];

    protected $casts = [
        'date_of_comeback' => 'date'
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
