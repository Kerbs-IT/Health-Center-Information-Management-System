<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class family_planning_medical_histories extends Model
{
    protected $fillable = [
        'case_id',
        'severe_headaches_migraine',
        'history_of_stroke',
        'non_traumatic_hemtoma',
        'history_of_breast_cancer',
        'severe_chest_pain',
        'cough',
        'jaundice',
        'unexplained_vaginal_bleeding',
        'abnormal_vaginal_discharge',
        'abnormal_phenobarbital',
        'smoker',
        'with_dissability',
        'if_with_dissability_specification',
    ];

    public function family_planning_case(){
        return $this->belongsTo(family_planning_case_records::class,'case_id','id');
    }
}
