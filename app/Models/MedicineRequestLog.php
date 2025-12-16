<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class MedicineRequestLog extends Model
{
    protected $fillable = [
        'medicine_request_id',
        'patient_name',
        'medicine_name',
        'dosage',
        'quantity',
        'performed_by_id',
        'performed_by_name',
        'performed_at',
        'action'
    ];

    protected $casts = [
        'performed_at' => 'datetime',
    ];

    public function request()
    {
        return $this->belongsTo(MedicineRequest::class, 'medicine_request_id');
    }
}
