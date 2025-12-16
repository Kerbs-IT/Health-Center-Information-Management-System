<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class senior_citizen_case_records extends Model
{
    protected $fillable = [
        'medical_record_case_id',
        'health_worker_id',
        'patient_name',
        'existing_medical_condition',
        'alergies',
        'prescribe_by_nurse',
        'remarks',
        'type_of_record',
        'status',
        'date_of_comeback'
    ];
    protected $casts = [
        'date_of_comeback' => 'date'
    ];
    public function medical_record_case()
    {
        return $this->belongsTo(medical_record_cases::class, 'medical_record_case_id', 'id');
    }
    public function health_worker()
    {
        return $this->belongsTo(staff::class, 'health_worker_id', 'user_id');
    }
    public function senior_citizen_maintenance_med(){
        return $this-> hasMany(senior_citizen_maintenance_meds::class, 'senior_citizen_case_id','id');
    }
}
