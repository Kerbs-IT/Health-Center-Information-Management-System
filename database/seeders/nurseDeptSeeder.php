<?php

namespace Database\Seeders;

use App\Models\nurse_department;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class nurseDeptSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $departments = [
            "General Consultation",
            "Maternal Care / Prenatal",
            "Child Care / Immunization",
            "Family Planning",
            "Health Education",
            "Wound Care / Minor Treatment",
            "TB / DOTS Program",
            "Nutrition Program",
            "Community Outreach",
            "Emergency / First Aid",
            "All-Around / General Duties"
        ];
        
        
        foreach($departments as $dept){
            $nurse_dept = nurse_department::create([
                'department' => $dept
            ]);
        }
    }
}
