<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class risk_for_sexually_transmitted_infections extends Model
{
    //
    protected $fillable = [
        'case_id',
        'infection_abnormal_discharge_from_genital_area',
        'origin_of_abnormal_discharge',
        'scores_or_ulcer',
        'pain_or_burning_sensation',
        'history_of_sexually_transmitted_infection',
        'sexually_transmitted_disease',
        'history_of_domestic_violence_of_VAW',
        'unpleasant_relationship_with_partner',
        'partner_does_not_approve',
        'referred_to',
        'reffered_to_others',
    ];

    public function family_planning_case(){
        return $this->belongsTo(family_planning_case_records::class,'case_id','id');
    }
}
