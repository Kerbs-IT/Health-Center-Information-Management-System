<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Medicine extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'medicines';
    protected $primaryKey = 'medicine_id';

    protected $fillable = [
        'medicine_name',
        'category_id',
        'category_name',
        'dosage',
        'type',
        'stock',
        'stock_status',
        'expiry_date',
        'expiry_status',
        'min_age_months',
        'max_age_months',
    ];

    protected $casts = [
        'expiry_date' => 'date',
        'min_age_months' => 'integer',
        'max_age_months' => 'integer',
    ];

    // Relationships
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id', 'category_id');
    }

    public function medicineRequests()
    {
        return $this->hasMany(MedicineRequest::class, 'medicine_id', 'medicine_id');
    }

    // Scopes
    public function scopeSearch($query, $term)
    {
        return $query->where('medicine_name', 'like', "%{$term}%")
            ->orWhere('dosage', 'like', "%{$term}%")
            ->orWhereHas('category', fn($q) => $q->where('category_name', 'like', "%{$term}%"));
    }
}