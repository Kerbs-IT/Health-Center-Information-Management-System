<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class family_planning_obsterical_histories extends Model
{
    protected $fillable = [
        'case_id',
        'G',
        'P',
        'full_term',
        'abortion',
        'premature',
        'living_children',
        'date_of_last_delivery',
        'type_of_last_delivery',
        'date_of_last_delivery_menstrual_period',
        'date_of_previous_delivery_menstrual_period',
        'type_of_menstrual',
        'Dysmenorrhea',
        'hydatidiform_mole',
        'ectopic_pregnancy',
    ];
    public function family_planning_case(){
        return $this->belongsTo(family_planning_case_records::class, 'cade_id', 'id');
    }
}
