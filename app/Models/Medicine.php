<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Medicine extends Model
{
    use HasFactory;
    protected $primaryKey ='medicine_id';
    protected $fillable = [
        'medicine_name',
        'category_id',
        'dosage',
        'stock',
        'expiry_date',
        'status'
    ];
    public function category(){
        return $this->belongsTo(Category::class, 'category_id', 'category_id');
    }

    public function scopeSearch($query, $value){
        $query->where('medicine_id', 'like', "%{$value}%")->orWhere('medicine_name', 'like', "%{$value}%")->orWhere('dosage', 'like', "%{$value}%")
        ->orWhere('stock', 'like',"%{$value}%")->orWhere('expiry_date', 'like', "%{$value}%")->orWhere('status', 'like', "%{$value}%")
        ->orWhereHas('category', function($cat) use ($value){
            $cat->where('category_name', 'like', "%{$value}%");
        });
    }
}
