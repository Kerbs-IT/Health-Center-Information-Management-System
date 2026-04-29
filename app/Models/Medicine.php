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
    ];

    protected $dates = ['deleted_at', 'expiry_date'];


    // ─── Relationships ───────────────────────────────────────────

    /**
     * Category — includes trashed so archived categories still resolve.
     */
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
     * Active batches sorted oldest-expiry-first (FIFO order).
     */
    public function batches()
    {
        return $this->hasMany(MedicineBatch::class, 'medicine_id', 'medicine_id')
                    ->where('quantity', '>', 0)
                    ->orderBy('expiry_date', 'asc');
    }

    /**
     * All batches regardless of remaining quantity.
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
    public function getFifoBatchAttribute(): ?MedicineBatch
    {
    return $this->batches()->first(); // already ordered by expiry_date asc, qty > 0
    }

}