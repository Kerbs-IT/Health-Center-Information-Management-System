<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class region extends Model
{
    //
    protected $primaryKey = 'code';
    public $incrementing = false;
    protected $keyType = 'string';

    public function provinces(){
        return $this-> hasMany([Province::class,'region_id','code']);
    }
    public function addresses(){
        return $this -> hasMany(addresses::class,'region_id', 'code');
    }
}
