<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class wra_masterlists extends Model
{
    //

    protected $fillable = [
        'health_worker_id',
        'address_id',
        'medical_record_case_id',
        'patient_id',
        'house_hold_number',
        'name_of_wra',
        'address',
        'age',
        'date_of_birth',
        'SE_status',
        'plan_to_have_more_children_yes',
        'plan_to_have_more_children_no',
        'current_FP_methods',
        'modern_FP',
        'traditional_FP',
        'currently_using_any_FP_method_no',
        'shift_to_modern_method',
        'wra_with_MFP_unmet_need',
        'wra_accept_any_modern_FP_method',
        'selected_modern_FP_method',
        'date_when_FP_method_accepted',
        'brgy_name',
        'status'
    ];

    public function health_worker()
    {
        return $this->belongsTo(staff::class, 'health_worker_id', 'user_id');
    }
    public function address_id()
    {
        return $this->hasOne(patient_addresses::class, 'address_id', 'id');
    }
    public function patient()
    {
        return $this->belongsTo(patients::class, 'patient_id', 'id');
    }
    public function medical_record_case()
    {
        return $this->belongsTo(medical_record_cases::class, 'medical_record_case_id', 'id');
    }
}
