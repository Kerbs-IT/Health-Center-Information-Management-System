<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;


class patients extends Model
{
    //
    protected $primaryKey = 'id'; // if the column is not the same like in 'user' table id and 'staff' user_id
    protected $keyType = 'int';
    protected $fillable = [
        'user_id',
        'patient_type',
        'first_name',
        'middle_initial',
        'last_name',
        'full_name',
        'age',
        'age_in_months',
        'date_of_birth',
        'sex',
        'address_id',
        'civil_status',
        'contact_number',
        'nationality',
        'profile_image',
        'date_of_registration',
        'place_of_birth',
        'status',
        'suffix'

    ];
    protected $casts = [
        'date_of_birth' => 'date',
        'date_of_registration' => 'date'
    ];

    public function address()
    {
        return $this->hasOne(patient_addresses::class, 'patient_id');
    }
    public function medical_record_case(){
        return $this->hasMany(medical_record_cases::class,'patient_id','id');
    }
    public function vaccination_masterlist(){
        return $this->hasOne(vaccination_masterlists::class,'patient_id','id');
    }
    //wra masterlist
    public function wra_masterlist(){
        return $this->hasOne(wra_masterlists::class, 'patient_id', 'id');
    }

    public function medicineRequests(){
        return $this->hasMany(MedicineRequest::class, 'patients_id', 'id');
    }

    // user
    public function user(){
        return $this->belongsTo(User::class,'patient_record_id','id');
    }

    public function isBound()
    {
        return !is_null($this->user_id);
    }

    // Get full name
    public function getFullNameAttribute()
    {
        $mi = $this->middle_initial ? substr($this->middle_initial, 0, 1) . '. ' : '';
        $suffix = $this->suffix? $this->suffix : '';
        return "{$this->first_name} {$mi}{$this->last_name} {$suffix}";
    }

    protected function ageDisplay():Attribute
    {
        return Attribute::make(
            get: function () {
                // If age is 0 and we have date_of_birth, calculate and display in months
                if ($this->age == 0 && $this->date_of_birth) {
                    // Use age_in_months if available, otherwise calculate from date_of_birth
                    $months = $this->age_in_months ?? (int) Carbon::parse($this->date_of_birth)->diffInMonths(Carbon::now());
                    return $months . ' month' . ($months != 1 ? 's' : '');
                }

                // Otherwise display in years
                return $this->age;
            }
        );
    }
    
}
