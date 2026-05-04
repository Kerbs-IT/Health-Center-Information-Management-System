<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Medicine extends Model
{
    use SoftDeletes;

    protected $primaryKey = 'medicine_id';

    protected $fillable = [
        'medicine_name',
        'category_id',
        'dosage',
        'stock',
        'expiry_date',
        'stock_status',
        'expiry_status',
        'min_age_months',
        'max_age_months',
        'auto_archived',
    ];

    protected $dates = ['deleted_at', 'expiry_date'];


    protected static function booted(): void
    {
        static::restoring(function (Medicine $medicine) {  // ← must be Medicine, not Category
            $category = Category::withTrashed()->find($medicine->category_id);

            if ($category && $category->trashed()) {
                throw new \RuntimeException(
                    "Cannot restore this medicine — its category \"{$category->category_name}\" is still archived. Restore the category first."
                );
            }
        });
    }

    // ─── Relationships ───────────────────────────────────────────

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id', 'category_id')
                    ->withTrashed();
    }

    public function medicine_request()
    {
        return $this->hasMany(MedicineRequest::class, 'medicine_id', 'medicine_id');
    }

    /**
     * Active, non-expired batches with remaining quantity — FIFO order.
     */
    public function batches()
    {
        return $this->hasMany(MedicineBatch::class, 'medicine_id', 'medicine_id')
                    ->where('expiry_date', '>', now())
                    ->where('quantity', '>', 0)
                    ->orderBy('expiry_date', 'asc');
    }

    /**
     * All batches regardless of quantity (for management views).
     */
    public function allBatches()
    {
        return $this->hasMany(MedicineBatch::class, 'medicine_id', 'medicine_id')
                    ->orderBy('expiry_date', 'asc');
    }

    // ─── Scopes ──────────────────────────────────────────────────

    public function scopeSearch($query, $value)
    {
        if (empty($value)) {
            return $query;
        }

        return $query->where('medicine_name', 'like', "%{$value}%")
            ->orWhereHas('category', function ($q) use ($value) {
                $q->withTrashed()
                  ->where('category_name', 'like', "%{$value}%");
            });
    }

    // ─── Accessors ───────────────────────────────────────────────

    /**
     * First valid batch in FIFO order (used for expiry status display).
     */
    public function getFifoBatchAttribute(): ?MedicineBatch
    {
        return $this->batches()->first();
    }

    /**
     * Total units physically in stock from non-expired batches.
     */
    public function getValidStockAttribute(): int
    {
        return $this->hasMany(MedicineBatch::class, 'medicine_id', 'medicine_id')
            ->where('expiry_date', '>', now())
            ->where('quantity', '>', 0)
            ->sum('quantity');
    }

    /**
     * Total units currently reserved (approved but not yet dispensed).
     * Sums reserved_quantity across all non-expired, non-trashed batches.
     */
    public function getTotalReservedAttribute(): int
    {
        return $this->hasMany(MedicineBatch::class, 'medicine_id', 'medicine_id')
            ->where('expiry_date', '>', now())
            ->sum('reserved_quantity');
    }

    /**
     * Units free to be requested or reserved right now.
     * = stock − total_reserved
     */
    public function getAvailableStockAttribute(): int
    {
        return max(0, $this->stock - $this->total_reserved);
    }

    public function getLastBatchAttribute(): ?MedicineBatch
    {
        return $this->hasMany(MedicineBatch::class, 'medicine_id', 'medicine_id')
            ->where('quantity', '>', 0)
            ->orderBy('expiry_date', 'desc')
            ->first();
    }
}