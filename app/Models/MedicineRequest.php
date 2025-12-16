<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MedicineRequest extends Model
{
    protected $primaryKey = 'id';

    protected $fillable = [
        'patients_id',
        'medicine_id',
        'quantity_requested',
        'reason',
        'status',
        'approved_by_id',
        'approved_by_type',
        'approved_at'
    ];
    protected $casts = [
        'requested_at' => 'datetime',
        'approved_at' => 'datetime',
    ];

    public function patients(){
        return $this->belongsTo(patients::class, 'patients_id', 'id');
    }
    public function medicine(){
        return $this->belongsTo(Medicine::class, 'medicine_id', 'medicine_id');
    }
    public function approver(){
        return $this->morphTo(__FUNCTION__, 'approved_by_type', 'approved_by_id');
    }

    public function logs()
    {
        return $this->hasMany(MedicineRequestLog::class, 'medicine_request_id');
    }

}
