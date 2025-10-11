<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class tb_dots_maintenance_medicines extends Model
{
    protected $fillable = [
        'tb_dots_case_id',
        'medicine_name',
        'dosage_n_frequency',
        'start_date',
        'end_date',
        'quantity'
    ];

    public function senior_citizen_maintenance_meds()
    {
        return $this->belongsTo(tb_dots_case_records::class, 'tb_dots_case_id', 'id');
    }
}
