<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use GuzzleHttp\Psr7\Request;
use Illuminate\Contracts\Auth\CanResetPassword;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements CanResetPassword
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'username',
        'email',
        'password',
        'role',
        'recovery_question',
        'recovery_answer',
        'status'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function nurses(){
        return $this-> hasOne(nurses::class,'user_id','id');
    }
    public function addresses(){
        return $this-> hasOne(addresses::class,'user_id','id');
    }
    public function admin_details(){
        return $this-> hasOne(admin_details::class,'user_id','id');
    }
    public function staff(){
        return $this -> hasOne(staff::class,'user_id','id');
    }
    public function patient(){
        return $this->hasOne(patients::class, 'user_id', 'id');
    }

    
}
