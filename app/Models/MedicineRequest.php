<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MedicineRequest extends Model
{
    protected $primaryKey = 'id';

    protected $fillable = [
        'patients_id',      // Nullable - for patients with full records
        'user_id',          // Nullable - for users without patient records
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

    /**
     * Get the patient associated with this request (if exists)
     */
    public function patients(){
        return $this->belongsTo(patients::class, 'patients_id', 'id');
    }

    /**
     * Get the user associated with this request (if no patient record)
     */
    public function user(){
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**
     * Get the medicine for this request
     */
    public function medicine(){
        return $this->belongsTo(Medicine::class, 'medicine_id', 'medicine_id');
    }

    /**
     * Get who approved this request
     */
    public function approver(){
        return $this->morphTo(__FUNCTION__, 'approved_by_type', 'approved_by_id');
    }

    /**
     * Get logs for this request
     */
    public function logs()
    {
        return $this->hasMany(MedicineRequestLog::class, 'medicine_request_id');
    }

    /**
     * Get the requester name (from patient or user)
     * This is a helper method to handle both cases
     */
    public function getRequesterNameAttribute()
    {
        if ($this->patients) {
            return $this->patients->full_name;
        }

        if ($this->user) {
            return $this->user->full_name;
        }

        return 'Unknown';
    }

    /**
     * Get the requester type
     */
    public function getRequesterTypeAttribute()
    {
        if ($this->patients_id) {
            return 'patient';
        }

        if ($this->user_id) {
            return 'user';
        }

        return 'unknown';
    }

    /**
     * Scope to get requests by requester (patient or user)
     */
    public function scopeForRequester($query, $userId)
    {
        return $query->where(function($q) use ($userId) {
            $q->whereHas('patients', function($p) use ($userId) {
                $p->where('user_id', $userId);
            })
            ->orWhere('user_id', $userId);
        });
    }
}