<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $primaryKey = 'category_id';
    protected $fillable = ['category_name'];

    public function medicines(){
        return $this->hasMany(Medicine::class);
    }
    public function scopeSearch($query, $value){
        $query->where('category_id', 'like', "%{$value}%")->orWhere('category_name', 'like', "%{$value}%");
    }
}
