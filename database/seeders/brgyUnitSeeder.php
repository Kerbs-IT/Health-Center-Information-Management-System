<?php

namespace Database\Seeders;

use App\Models\brgy_unit;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class brgyUnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $data = [
            'Karlaville Park Homes',
            'Purok 1',
            'Purok 2',
            'Purok 3',
            'Purok 4',
            'Purok 5',
            'Purok 6',
            'Beverly Homes 1',
            'Beverly Homes 2',
            'Green Forbes City',
            'Gawad Kalinga',
            'Kaia Homes Phase 2',
            'Heneral DOS',
            'SUGAR LAND'
            ];

        foreach($data as $unit){
            $brgy_unit = brgy_unit::create([
                'brgy_unit' => $unit
            ]);
        }
    }
}
