<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Barangay extends Model
{
    //
    protected $primaryKey = 'code';
    public $incrementing = true;
    protected $keyType = 'int';

    public function city(){
        return $this -> belongsTo(City::class,'city_id','code');
    }

    public function addresses(){
        return $this -> hasMany(addresses::class,'brgy_id', 'code');
    }
}
