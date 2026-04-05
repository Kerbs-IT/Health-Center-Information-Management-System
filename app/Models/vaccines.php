<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class vaccines extends Model
{
    //

    protected $fillable = [
        'type_of_vaccine',
        'vaccine_acronym',
        'max_doses',
        'status',
    ];
    
    protected $casts = [
        'max_doses' => 'integer',
    ];

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'Active');
    }

    public function scopeArchived($query)
    {
        return $query->where('status', 'Archived');
    }

    // Relationships
    public function vaccine_administered(){
        return $this -> hasMany(vaccineAdministered::class,'vaccine_id','id');
    }
}
