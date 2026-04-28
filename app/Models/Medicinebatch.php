<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
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
        'reserved_quantity',
        'price',
        'manufactured_date',
        'expiry_date',
        'expiry_status',
    ];

    protected $casts = [
        'expiry_date'      => 'date',
        'manufactured_date'=> 'date',
    ];

    // ─── Relationships ───────────────────────────────────────────

    public function medicine()
    {
        return $this->belongsTo(Medicine::class, 'medicine_id', 'medicine_id');
    }

    // ─── Helpers ─────────────────────────────────────────────────

    /**
     * Recalculate and persist expiry_status based on expiry_date.
     */
    public function recalculateExpiryStatus(): void
    {
        $days = now()->diffInDays($this->expiry_date, false);

        if ($days < 0) {
            $status = 'Expired';
        } elseif ($days <= 30) {
            $status = 'Expiring Soon';
        } else {
            $status = 'Valid';
        }

        $this->update(['expiry_status' => $status]);
    }
}