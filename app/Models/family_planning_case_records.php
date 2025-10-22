<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class family_planning_case_records extends Model
{
    protected $fillable = [
        'medical_record_case_id',
        'health_worker_id',
        'client_id',
        'philhealth_no',
        'NHTS',
        'client_name',
        'client_date_of_birth',
        'client_age',
        'occupation',
        'client_address',
        'client_contact_number',
        'client_civil_status',
        'client_religion',
        'spouse_lname',
        'spouse_fname',
        'spouse_MI',
        'spouse_date_of_birth',
        'spouse_age',
        'spouse_occupation',
        'number_of_living_children',
        'plan_to_have_more_children',
        'average_montly_income',
        'type_of_patient',
        'new_acceptor_reason_for_FP',
        'current_user_reason_for_FP',
        'current_method_reason',
        'previously_used_method',
        'choosen_method',
        'signature_image',
        'date_of_acknowledgement',
        'acknowledgement_consent_signature_image',
        'date_of_acknowledgement_consent',
        'type_of_record',
        'status',
        'current_user_type'
    ];

    public function medical_record_case()
    {
        return $this->belongsTo(medical_record_cases::class, 'medical_record_case_id', 'id');
    }
    public function health_worker()
    {
        return $this->belongsTo(staff::class, 'health_worker_id', 'user_id');
    }
    // the connection of other tables
    public function medical_history(){
        return $this->hasOne(family_planning_medical_histories::class,'case_id','id');
    }
    public function obsterical_history(){
        return $this->hasOne(family_planning_obsterical_histories::class, 'case_id', 'id');
    }
    public function risk_for_sexually_transmitted_infection(){
        return $this->hasOne(risk_for_sexually_transmitted_infections::class,'case_id','id');
    }
    public function physical_examinations(){
        return $this->hasOne(family_planning_physical_examinations::class,'case_id','id');
    }
}
