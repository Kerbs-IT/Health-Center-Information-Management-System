<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class brgy_unit extends Model
{
    //
    protected $primaryKey = 'id';
    protected $fillable = [
        'brgy_unit'
    ];

    public function staff(){
        return $this-> hasMany(staff::class,'brgy_unit_id','id');
    }
    
}
