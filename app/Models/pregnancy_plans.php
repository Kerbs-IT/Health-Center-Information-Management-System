<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class pregnancy_plans extends Model
{
    //

    protected $fillable = [
        'medical_record_case_id',
        'patient_name',
        'midwife_name',
        'place_of_birth',
        'authorized_by_philhealth',
        'cost_of_pregnancy',
        'payment_method',
        'transportation_mode',
        'accompany_person_to_hospital',
        'accompany_through_pregnancy',
        'care_person',
        'emergency_person_name',
        'emergency_person_residency',
        'emergency_person_contact_number',
        'signature',
        'type_of_record',
        'status'

    ];

    public function medical_case_record(){
        return $this-> belongsTo(medical_record_cases::class, 'medical_case_record_id','id');
    }
    public function donor_name(){
        return $this-> hasMany(donor_names::class,'pregnancy_plan_id','id');
    }
}
