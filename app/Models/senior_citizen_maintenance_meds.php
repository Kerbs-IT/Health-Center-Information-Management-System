<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class senior_citizen_maintenance_meds extends Model
{

    protected $fillable = [
        'senior_citizen_case_id',
        'maintenance_medication',
        'dosage_n_frequency',
        'start_date',
        'end_date',
        'quantity'
    ];

    public function senior_citizen_maintenance_meds(){
        return $this -> belongsTo(senior_citizen_case_records::class,'senior_citizen_case_id','id');
    }
}
