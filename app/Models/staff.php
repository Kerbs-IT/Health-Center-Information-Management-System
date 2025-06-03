<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class staff extends Model
{
    //
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
        return $this -> belongsTo(brgy_unit::class,'assigned_area_id','brgy_id');
    }
}
