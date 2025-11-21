<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class vaccination_masterlists extends Model
{
    //
    protected $fillable = [
        'brgy_name',
        'midwife',
        'health_worker_id',
        'medical_record_case_id',
        'name_of_child',
        'address_id',
        'patient_id',
        'Address',
        'sex',
        'age',
        'date_of_birth',
        'SE_status',
        'BCG',
        'Hepatitis B',
        'PENTA_1',
        'PENTA_2',
        'PENTA_3',
        'OPV_1',
        'OPV_2',
        'OPV_3',
        'PCV_1',
        'PCV_2',
        'PCV_3',
        'IPV_1',
        'IPV_2',
        'MCV_1',
        'MCV_2',
        'remarks'
    ];
    protected $casts = [
        'date_of_birth' => 'date'
    ];

    public function health_worker()
    {
        return $this->belongsTo(staff::class, 'health_worker_id', 'user_id');
    }
    public function address_id(){
        return $this->hasOne(patient_addresses::class,'address_id', 'id');
    }
    public function patient(){
        return $this->belongsTo(patients::class,'patient_id', 'id');
    }
    public function medical_record_case(){
        return $this->belongsTo(medical_record_cases::class, 'medical_record_case_id','id');
    }
}
