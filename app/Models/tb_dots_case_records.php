<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class tb_dots_case_records extends Model
{
    protected $fillable = [
        'medical_record_case_id',
        'health_worker_id',
        'patient_name',
        'type_of_tuberculosis',
        'type_of_tb_case',
        'date_of_diagnosis',
        'name_of_physician',
        'sputum_test_results',
        'treatment_category',
        'date_administered',
        'side_effect',
        'remarks',
        'outcome',
        'type_of_record',
        'status'
    ];

    public function medical_record_case()
    {
        return $this->belongsTo(medical_record_cases::class, 'medical_record_case_id', 'id');
    }
    public function health_worker()
    {
        return $this->belongsTo(staff::class, 'health_worker_id', 'user_id');
    }

    public function tb_dots_maintenance_med()
    {
        return $this->hasMany(tb_dots_maintenance_medicines::class, 'tb_dots_case_id', 'id');
    }
}
