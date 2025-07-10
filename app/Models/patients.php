<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class patients extends Model
{
    //
    protected $primaryKey = 'user_id'; // if the column is not the same like in 'user' table id and 'staff' user_id
    protected $keyType = 'int';
    protected $fillable = [
        'user_id',
        'patient_type',
        'first_name',
        'middle_initial',
        'last_name',
        'full_name',
        'age',
        'date_of_birth',
        'sex',
        'address_id',
        'civil_status',
        'contact_number',
        'nationality',
        'profile_image',

    ];
}
