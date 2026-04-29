<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MedicineRequestLog extends Model
{
    protected $fillable = [
        'medicine_request_id',
        'patient_name',
        'medicine_name',
        'dosage',
        'quantity',
        'batches_used',
        'action',
        'performed_by_id',
        'performed_by_name',
        'performed_at',
    ];

    protected $casts = [
        'performed_at' => 'datetime',
        'batches_used' => 'array',
    ];

    public function request()
    {
        return $this->belongsTo(MedicineRequest::class, 'medicine_request_id');
    }
}