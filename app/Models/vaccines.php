<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class vaccines extends Model
{
    //

    protected $fillable = [
        'type_of_vaccine',
        'vaccine_acronym'
    ];
    public function vaccine_administered(){
        return $this -> hasMany(vaccineAdministered::class,'vaccine_id','id');
    }
}
