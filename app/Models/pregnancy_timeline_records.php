<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class pregnancy_timeline_records extends Model
{
    protected $fillable = [
        'prenatal_case_record_id',
        'year',
        'type_of_delivery',
        'place_of_delivery',
        'birth_attendant',
        'compilation',
        'outcome',
        
    ];

    public function prenatal_case_records(){
        return $this-> belongsTo(prenatal_case_records::class, 'prenatal_case_record_id','id');
    }
}
