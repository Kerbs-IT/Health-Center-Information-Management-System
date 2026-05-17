<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\patient_addresses;  // ← Changed this
use App\Observers\PatientAddressObserver;
use App\Models\Medicine;
use App\Observers\MedicineObserver;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {

    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
        Medicine::observe(MedicineObserver::class);
        patient_addresses::observe(PatientAddressObserver::class);  // ← Changed this
    }
}
