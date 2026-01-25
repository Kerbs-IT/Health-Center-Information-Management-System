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
        'max_age_months'
    ];

    protected $dates = ['deleted_at', 'expiry_date'];

    /**
     * Relationship with Category - includes trashed categories
     */
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id', 'category_id')
                    ->withTrashed(); // This allows accessing archived categories
    }

    /**
     * Relationship with MedicineRequest
     */
    public function medicine_request()
    {
        return $this->hasMany(MedicineRequest::class, 'medicine_id', 'medicine_id');
    }

    /**
     * Scope for searching medicines
     * Includes searching in trashed categories
     */
    public function scopeSearch($query, $value)
    {
        if (empty($value)) {
            return $query;
        }

        return $query->where('medicine_name', 'like', "%{$value}%")
            ->orWhereHas('category', function ($q) use ($value) {
                $q->withTrashed() // Include trashed categories in search
                  ->where('category_name', 'like', "%{$value}%");
            });
    }
}