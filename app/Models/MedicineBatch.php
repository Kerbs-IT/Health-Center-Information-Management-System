<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MedicineBatch extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'medicine_id',
        'batch_number',
        'quantity',
        'initial_quantity',
        'dispensed_quantity',
        'reserved_quantity',   // ← units locked by approved requests
        'price',
        'manufactured_date',
        'expiry_date',
        'expiry_status',
    ];

    protected $casts = [
        'expiry_date'       => 'date',
        'manufactured_date' => 'date',
    ];

    // ─── Relationships ───────────────────────────────────────────

    public function medicine()
    {
        return $this->belongsTo(Medicine::class, 'medicine_id', 'medicine_id');
    }

    // ─── Computed helpers ─────────────────────────────────────────

    /**
     * Units physically available to reserve or dispense.
     * = quantity - reserved_quantity
     */
    public function getAvailableQuantityAttribute(): int
    {
        return max(0, $this->quantity - $this->reserved_quantity);
    }

    // ─── Helpers ─────────────────────────────────────────────────

    /**
     * Recalculate and persist expiry_status based on expiry_date.
     */
    public function recalculateExpiryStatus(): void
    {
        $expiry = $this->expiry_date->copy()->startOfDay();
        $today  = now('Asia/Manila')->startOfDay();

        if ($expiry->lte($today)) {
            $status = 'Expired';
        } elseif ($expiry->lte($today->copy()->addDays(30))) {
            $status = 'Expiring Soon';
        } else {
            $status = 'Valid';
        }

        $this->update(['expiry_status' => $status]);
    }
}