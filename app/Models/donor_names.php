<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class donor_names extends Model
{
    //
    protected $fillable = [
        'pregnancy_plan_id',
        'donor_name'
    ];

    public function pregnancy_plan(){
        return $this-> belongsTo(pregnancy_plans::class,'pregnancy_plan_id','id');
    }
}
