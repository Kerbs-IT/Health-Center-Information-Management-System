<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    //

    protected $primaryKey = 'code';
    public $incrementing = true;
    protected $keyType = 'int';

    public function province(){
        return $this-> belongsTo(Province::class,'province_id','code');
    }

    public function barangay(){
        return $this -> hasMany(Barangay::class,'city_id','code');
    }
    public function addresses(){
        return $this -> hasMany(addresses::class,'city_id', 'code');
    }
}
