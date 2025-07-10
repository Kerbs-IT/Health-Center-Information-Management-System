<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class addresses extends Model
{
    //
    protected $fillable = [
        'user_id',
        'street',
        'brgy_id',
        'city_id',
        'province_id',
        'region_id',
        'postal_code',
        'role'

    ];
    protected $primaryKey = 'address_id';

    public function barangay(){
        return $this-> belongsTo(Barangay::class,'brgy_id','code');
    }

    public function cities(){
        return $this-> belongsTo(City::class,'city_id','code');
    }
    public function province(){
        return $this-> belongsTo(Province::class,'province_id','code');
    }
    public function region(){
        return $this-> belongsTo(region::class,'region_id', 'code');
    }

    public function nurses(){
        return $this -> hasOne(nurses::class,'address_id','address_id');
    }
    public function user(){
        return $this -> belongsTo(User::class,'user_id','id');
    }
    public function staff(){
        return $this -> hasOne(staff::class,'address_id','address_id');
    }
    // patients
    public function patient(){
        return $this -> hasOne(patients::class,'address_id','address_id');
    }
}
