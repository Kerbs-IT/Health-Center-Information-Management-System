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
            $now = now();

            $category->medicines()
                ->whereNull('deleted_at')
                ->each(function (Medicine $medicine) use ($now) {
                    $medicine->deleted_at = $now;
                    $medicine->saveQuietly();
                });
        });

        static::restoring(function (Category $category) {
            $archivedAt = $category->deleted_at;

            Medicine::onlyTrashed()
                ->where('category_id', $category->category_id)
                ->whereBetween('deleted_at', [
                    $archivedAt->copy()->subSeconds(5),
                    $archivedAt->copy()->addSeconds(5),
                ])
                ->each(fn(Medicine $medicine) => $medicine->restore());
        });
    }
}