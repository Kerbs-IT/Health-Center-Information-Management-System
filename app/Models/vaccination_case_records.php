<?php

namespace App\Models;

use Database\Seeders\vaccineSeeder;
use Illuminate\Database\Eloquent\Model;

class vaccination_case_records extends Model
{
    //
    protected $fillable =[
        'medical_record_case_id',
        'patient_name',
        'administered_by',
        'date_of_vaccination',
        'time',
        'vaccine_type',
        'dose_number',
        'remarks',
        'type_of_record',
        'health_worker_id',
        'status',
        'height',
        'weight',
        'temperature',
        'date_of_comeback',
        'vaccination_status'

    ];

    public function medical_case_record(){
        return $this->belongsTo(medical_record_cases::class,'medical_record_case_id','id');
    }
    public function health_worker(){
        return $this->belongsTo(staff::class,'health_worker_id','user_id');
    }
    public function vaccine_administered(){
        return $this->hasMany(vaccineAdministered::class, 'vaccination_case_record_id','id');
    }
}
