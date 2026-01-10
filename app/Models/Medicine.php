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

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id', 'category_id');
    }

    public function scopeSearch($query, $value)
    {
        return $query->where('medicine_name', 'like', "%{$value}%")
            ->orWhereHas('category', function ($q) use ($value) {
                $q->where('category_name', 'like', "%{$value}%");
            });
    }
}