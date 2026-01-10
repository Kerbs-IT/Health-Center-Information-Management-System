<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class vaccination_masterlists extends Model
{
    //
    protected $fillable = [
        'brgy_name',
        'midwife',
        'health_worker_id',
        'medical_record_case_id',
        'name_of_child',
        'address_id',
        'patient_id',
        'Address',
        'sex',
        'age',
        'age_in_months',
        'date_of_birth',
        'SE_status',
        'BCG',
        'Hepatitis B',
        'PENTA_1',
        'PENTA_2',
        'PENTA_3',
        'OPV_1',
        'OPV_2',
        'OPV_3',
        'PCV_1',
        'PCV_2',
        'PCV_3',
        'IPV_1',
        'IPV_2',
        'MCV_1',
        'MCV_2',
        'remarks',
        'status'
    ];
    protected $casts = [
        'date_of_birth' => 'date'
    ];

    public function health_worker()
    {
        return $this->belongsTo(staff::class, 'health_worker_id', 'user_id');
    }
    public function address_id(){
        return $this->hasOne(patient_addresses::class,'address_id', 'id');
    }
    public function patient(){
        return $this->belongsTo(patients::class,'patient_id', 'id');
    }
    public function medical_record_case(){
        return $this->belongsTo(medical_record_cases::class, 'medical_record_case_id','id');
    }

    // Use the new Laravel 11 Attribute syntax
    protected function ageDisplay(): Attribute
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
                return $this->age . ' year' . ($this->age != 1 ? 's' : '');
            }
        );
    }
}
