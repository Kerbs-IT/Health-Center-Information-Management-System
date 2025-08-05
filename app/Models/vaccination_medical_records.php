<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class vaccination_medical_records extends Model
{
    protected $fillable =[
        'medical_record_case_id',
        'date_of_registration',
        'administered_by',
        'mother_name',
        'father_name',
        'birth_height',
        'birth_weight',
        'type_of_record',
        'health_worker_id'
    ];

    public function medical_record_case(){
        return $this-> belongsTo(medical_record_cases::class,'medical_record_case_id','id');
    }
    public function health_worker()
    {
        return $this->belongsTo(staff::class, 'health_worker_id', 'user_id');
    }
}
