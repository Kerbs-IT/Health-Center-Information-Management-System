<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class family_planning_physical_examinations extends Model
{
    protected $fillable = [
        'case_id',
        'blood_pressure',   
        'pulse_rate',     
        'height',   
        'weight',
        'neck_type',
        'skin_type',
        'conjuctiva_type',
        'breast_type',
        'abdomen_type',
        'extremites_type',
        'extremites_UID_type',
        'cervical_abnormalities_type',
        'cervical_consistency_type',
        'uterine_position_type',
        'uterine_depth_text',
    ];

    public function family_planning_case(){
        return $this->belongsTo(family_planning_case_records::class,'case_id', 'id');
    }
}
