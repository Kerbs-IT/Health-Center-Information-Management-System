<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class family_planning_side_b_records extends Model
{
    //
    protected $fillable = [
        'medical_record_case_id',
        'health_worker_id',
        'date_of_visit',
        'medical_findings',
        'method_accepted',
        'signature_of_the_provider',
        'date_of_follow_up_visit',
        'baby_Less_than_six_months_question',
        'sexual_intercouse_or_mesntrual_period_question',
        'baby_last_4_weeks_question',
        'menstrual_period_in_seven_days_question',
        'miscarriage_or_abortion_question',
        'contraceptive_question',
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
