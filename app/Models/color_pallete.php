<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class color_pallete extends Model
{
    //
    protected $fillable = [
        'primaryColor',
        'secondaryColor',
        'tertiaryColor'
    ];
}
