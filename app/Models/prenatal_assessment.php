<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class prenatal_assessment extends Model
{
    protected $fillable = [
        'prenatal_case_record_id',
        'spotting',
        'edema',
        'severe_headache',
        'blumming_vission',
        'water_discharge',
        'severe_vomiting',
        'hx_smoking',
        'alchohol_drinker',
        'drug_intake'
    ];

    public function prenatal_case_record(){
        return $this->belongsTo(prenatal_case_records::class, 'prenatal_case_record_id', 'id');
    }
}
