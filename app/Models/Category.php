<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use SoftDeletes;

    protected $primaryKey = 'category_id';
    protected $fillable = ['category_name'];

    // Add deleted_at to dates
    protected $dates = ['deleted_at'];

    public function scopeSearch($query, $value)
    {
        return $query->where('category_name', 'like', "%{$value}%");
    }
}