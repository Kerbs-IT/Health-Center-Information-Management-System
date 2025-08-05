<?php

namespace Database\Seeders;

use App\Models\vaccines;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class vaccineSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $data = [
            ['BCG Vaccine', 'BCG'],
            ['Hepatitis B Vaccine', 'Hepatitis B'],
            ['Pentavalent Vaccine (DPT-HEP B-HIB)','Penta'],
            ['Oral Polio Vaccine (OPV)','OPV'],
            ['Inactived Polio Vaccine (IPV)','IPV'],
            ['Pnueumococcal Conjugate Vaccine (PCV)','PCV'],
            ['Measles, Mumps, Rubella Vaccine (MMR)','MMR'],
            ['Measles Containing Vaccine (MCV) MR/MMR (Grade 1)', '(MCV) MR/MMR (Grade 1)'],
            ['Measles Containing Vaccine (MCV) MR/MMR (Grade 7)', '(MCV) MR/MMR (Grade 7)'],
            ['Tetanus Diphtheria (TD)','TD'],
            ['Human Papiliomavirus Vaccine','Human Papiliomavirus'],
            ['Influenza Vaccine','Influenza Vaccine'],
            ['Pnuemococcal Vaccine', 'Pnuemococcal Vaccine']
        ];

        foreach ($data as $vaccine) {
            $vaccines = vaccines::create([
                'type_of_vaccine' => $vaccine[0],
                'vaccine_acronym' => $vaccine[1]
            ]);
        };
    }
}
