<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use SoftDeletes;

    protected $primaryKey = 'category_id';
    protected $fillable = ['category_name'];
    protected $dates = ['deleted_at'];

    public function medicines()
    {
        return $this->hasMany(Medicine::class, 'category_id', 'category_id');
    }

    public function scopeSearch($query, $value)
    {
        return $query->where('category_name', 'like', "%{$value}%");
    }

    protected static function booted(): void
    {
        static::deleting(function (Category $category) {
            // Mark auto-archived so we can distinguish from manual archives
            $category->medicines()
                ->whereNull('deleted_at')
                ->each(function (Medicine $medicine) {
                    $medicine->auto_archived = true;
                    $medicine->deleted_at    = now();
                    $medicine->saveQuietly();
                });
        });

        static::restoring(function (Category $category) {
            Medicine::onlyTrashed()
                ->where('category_id', $category->category_id)
                ->where('auto_archived', true)
                ->each(function (Medicine $medicine) {
                    $medicine->auto_archived = false;
                    $medicine->deleted_at    = null;
                    $medicine->saveQuietly();
                });
        });
    }
}