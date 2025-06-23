<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class nurses extends Model
{
    //
    protected $primaryKey = 'user_id'; // if the column is not the same like in 'user' table id and 'nurse' user_id
    protected $keyType = 'int'; 
    protected $fillable = [
        'user_id',
        'first_name',
        'middle_initial',
        'last_name',
        'full_name',
        'department',
        'address_id',
        'profile_image',
        'age',
        'date_of_birth',
        'sex',
        'civil_status',
        'contact_number',
        'nationality',

    ];

    public function addresses(){
        return $this -> belongsTo(addresses::class,'address_id','address_id');
    }
    public function user(){
        return $this-> belongsTo(User::class,'user_id','id');
    }
}
