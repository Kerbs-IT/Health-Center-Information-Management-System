<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MedicineRequest extends Model
{
    protected $primaryKey = 'id';

    protected $fillable = [
        'patients_id',
        'user_id',
        'medicine_id',
        'quantity_requested',
        'reason',
        'status',
        'approved_by_id',
        'approved_by_type',
        'approved_at',
        'ready_at',
        'dispensed_at',
        'dispensed_by_id',
        // ── Add these ──
        'reserved_quantity',
        'batches_snapshot',
        'reserved_at',
        'cancelled_at',
        'cancellation_reason',
    ];

    protected $casts = [
        'requested_at'    => 'datetime',
        'approved_at'     => 'datetime',
        'ready_at'        => 'datetime',
        'dispensed_at'    => 'datetime',
        // ── Add these ──
        'reserved_at'     => 'datetime',
        'cancelled_at'    => 'datetime',
        'batches_snapshot'=> 'array',  // handles JSON encode/decode automatically
    ];


    // ─── Relationships ───────────────────────────────────────────

    public function patients()
    {
        return $this->belongsTo(patients::class, 'patients_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function medicine()
    {
        return $this->belongsTo(Medicine::class, 'medicine_id', 'medicine_id')
                    ->withTrashed();
    }

    public function approver()
    {
        return $this->morphTo(__FUNCTION__, 'approved_by_type', 'approved_by_id');
    }

    public function logs()
    {
        return $this->hasMany(MedicineRequestLog::class, 'medicine_request_id');
    }

    // ─── Accessors ───────────────────────────────────────────────

    public function getRequesterNameAttribute(): string
    {
        if ($this->patients) {
            return $this->patients->full_name;
        }

        if ($this->user) {
            return $this->user->full_name;
        }

        return 'Unknown';
    }

    public function getRequesterTypeAttribute(): string
    {
        if ($this->patients_id) return 'patient';
        if ($this->user_id)     return 'user';
        return 'unknown';
    }

    // ─── Scopes ──────────────────────────────────────────────────

    public function scopeForRequester($query, $userId)
    {
        return $query->where(function ($q) use ($userId) {
            $q->whereHas('patients', fn($p) => $p->where('user_id', $userId))
              ->orWhere('user_id', $userId);
        });
    }
}