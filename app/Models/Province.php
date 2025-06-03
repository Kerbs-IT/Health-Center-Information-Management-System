<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Province extends Model
{
    //
    protected $primaryKey = 'code';
    public $incrementing = true;
    protected $keyType = 'int';

    public function region(){
        return $this-> belongsTo(region::class, 'region_id','code');
    }  

    public function cities(){
        return $this-> hasMany(City::class,'province_id','code');
    }
    public function addresses(){
        return $this -> hasMany(addresses::class,'province_id', 'code');
    }
}
