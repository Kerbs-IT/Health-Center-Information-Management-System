<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class vaccineAdministered extends Model
{
    protected $fillable = [
        'vaccination_case_record_id',
        'vaccine_type',
        'dose_number'
    ];

    public function vaccination_medical_record(){
        return $this ->belongsTo(vaccination_case_records::class, 'vaccination_case_record_id','id');
    }
}
