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
        'patient_type',
        'first_name',
        'last_name',
        'middle_initial',
        'full_name',
        'date_of_birth',
        'contact_number',
        'address',
        'role',
        'recovery_question',
        'recovery_answer',
        'status',
        'patient_record_id',
        'is_verified',
        'verification_code',
        'verification_code_expires_at',
        'verification_attempts',
        'verification_locked_until',
        'email_verified_at',
        'profile_image',
        'suffix'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'verification_code'
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
            'date_of_birth' => 'date',
            'password' => 'hashed',
            'email_verified_at' => 'datetime',
            'verification_code_expires_at' => 'datetime',
            'verification_locked_until' => 'datetime',
            'is_verified' => 'boolean',
        ];
    }

    public function nurses()
    {
        return $this->hasOne(nurses::class, 'user_id', 'id');
    }
    public function addresses()
    {
        return $this->hasOne(addresses::class, 'user_id', 'id');
    }
    public function admin_details()
    {
        return $this->hasOne(admin_details::class, 'user_id', 'id');
    }
    public function staff()
    {
        return $this->hasOne(staff::class, 'user_id', 'id');
    }
    public function patient()
    {
        return $this->hasOne(patients::class, 'user_id', 'id');
    }
    public function medicineRequest()
    {
        return $this->hasMany(MedicineRequest::class, 'user_id', 'id');
    }

    // Check if bound
    public function isBound()
    {
        return !is_null($this->patient_record_id);
    }

    public function getFullNameAttribute()
    {
        $mi = $this->middle_initial ? substr($this->middle_initial, 0, 1) . '. ' : '';
        $suffix = $this->suffix ? $this->suffix : '';
        return "{$this->first_name} {$mi}{$this->last_name} {$suffix}";
    }

    // users address
    public function user_address()
    {
        return $this->hasOne(users_address::class, 'user_id', 'id');
    }
}
