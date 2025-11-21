<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class staff extends Model
{
    //

    protected $primaryKey = 'user_id'; // if the column is not the same like in 'user' table id and 'staff' user_id
    protected $keyType = 'int'; 
    protected $fillable = [
        'user_id',
        'first_name',
        'middle_initial',
        'last_name',
        'full_name',
        'assigned_area_id',
        'address_id',
        'profile_image',
        'age',
        'date_of_birth',
        'sex',
        'civil_status',
        'contact_number',
        'nationality',

    ];

    public function user(){
        return $this-> belongsTo(User::class,'user_id','id');
    }

    public function addresses(){
        return $this -> belongsTo(addresses::class,'address_id','address_id');
    }
    public function assigned_area(){
        return $this -> belongsTo(brgy_unit::class,'assigned_area_id','id');
    }
    public function vaccination_case_records(){
        return $this-> hasMany(vaccination_case_records::class,'health_worker_id','user_id');
    }
    public function vaccination_medical_records()
    {
        return $this->hasMany(vaccination_case_records::class, 'health_worker_id', 'user_id');
    }
    // prenatal
    public function prenatal_medical_records(){
        return $this->hasMany(prenatal_medical_records::class, 'health_worker_id', 'user_id');
    }
    public function prenatal_case_records()
    {
        return $this->hasMany(prenatal_medical_records::class, 'health_worker_id', 'user_id');
    }
    public function prenatal_checkup(){
        return $this-> hasMany(pregnancy_checkups::class,'health_worker_id','user_id');
    }

    // senior citizen
    public function senior_citizen_medical_records(){
        return $this-> hasMany(senior_citizen_medical_records::class, 'health_worker_id', 'user_id');

    }
    public function senior_citizen_case_records()
    {
        return $this->hasMany(senior_citizen_case_records::class, 'health_worker_id', 'user_id');
    }

    // senior citizen
    public function tb_dots_medical_records()
    {
        return $this->hasMany(tb_dots_medical_records::class, 'health_worker_id', 'user_id');
    }
    public function tb_dots_case_records()
    {
        return $this->hasMany(tb_dots_case_records::class, 'health_worker_id', 'user_id');
    }

    // tb dots checkuo
    public function tb_dots_checkup()
    {
        return $this->hasMany(tb_dots_check_ups::class, 'health_worker_id', 'user_id');
    }

    // family planning

    public function family_planning_medical_records(){
        return $this->hasMany(family_planning_medical_records::class, 'health_worker_id', 'user_id');
    }
    public function family_planning_case_records()
    {
        return $this->hasMany(family_planning_case_records::class, 'health_worker_id', 'user_id');
    }
    public function family_planning_side_b_record()
    {
        return $this->hasMany(family_planning_side_b_records::class, 'health_worker_id', 'user_id');
    }

    // vaccination masterlist
    public function vaccination_masterlist(){
        return $this->hasMany(vaccination_masterlists::class, 'health_worker_id','id');
    }
    
    
}
