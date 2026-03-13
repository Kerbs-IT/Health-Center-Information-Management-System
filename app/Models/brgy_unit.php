<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class brgy_unit extends Model
{
    //
    protected $primaryKey = 'id';
    protected $fillable = [
        'brgy_unit',
        'status'
    ];

    public function staff(){
        return $this-> hasMany(staff::class, 'assigned_area_id','id');
    }
    
}
