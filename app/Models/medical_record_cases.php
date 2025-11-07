<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class medical_record_cases extends Model
{
    //
    protected $fillable = [
        'patient_id',
        'type_of_case'
    ];

    public function patient(){
        return $this-> belongsTo(patients::class,'patient_id','id');
    }
    public function vaccination_medical_record(){
        return $this-> hasOne(vaccination_medical_records::class,'medical_record_case_id','id');
    }
    public function vaccination_case_record(){
        return $this->hasMany(vaccination_case_records::class, 'medical_record_case_id','id');
    }
    // prenatal 
    public function prenatal_medical_record(){
        return $this-> hasOne(prenatal_medical_records::class, 'medical_record_case_id', 'id');
    }
    public function prenatal_case_record(){
        return $this-> hasMany(prenatal_case_records::class, 'medical_record_case_id', 'id');
    }
    public function pregnancy_plan(){
        return $this-> hasOne(pregnancy_plans::class, 'medical_record_case_id', 'id');
    }
    // prental check-up
    public function pregnancy_checkup(){
        return $this->hasMany(pregnancy_checkups::class,'medical_record_case_id','id');
    }

    // senior citizen

    public function senior_citizen_medical_record(){
        return $this-> hasOne(senior_citizen_medical_records::class, 'medical_record_case_id','id');
    }
    public function senior_citizen_case_record(){
        return $this-> hasMany(senior_citizen_case_records::class, 'medical_record_case_id', 'id');
    }

    // td dots
    public function tb_dots_medical_record(){
        return $this-> hasOne(tb_dots_medical_records::class, 'medical_record_case_id', 'id');
    }
    public function tb_dots_case_record()
    {
        return $this->hasMany(tb_dots_case_records::class, 'medical_record_case_id', 'id');
    }
    // tb dots checkuo
    public function tb_dots_checkup(){
        return $this->hasMany(tb_dots_check_ups::class, 'medical_record_case_id', 'id');
    }

    // family planning 

    public function family_planning_medical_record(){
        return $this->hasOne(family_planning_medical_records::class, 'medical_record_case_id', 'id');
    }
    public function family_planning_case_record(){
        return $this->hasOne(family_planning_case_records::class, 'medical_record_case_id', 'id');
    }
    // family planning side b case
    public function family_planning_side_b_record(){
        return $this->hasOne(family_planning_side_b_records::class, 'medical_record_case_id', 'id');
    }
    
}
