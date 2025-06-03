<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

use App\Models\region;
use App\Models\Province;
use App\Models\City;
use App\Models\Barangay;

class AddressSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $data = json_decode(File::get(database_path('data/philippine_address.json')),true);

        foreach($data as $regionCode => $regionData){
            $region = region::create([
                'code' => $regionCode,
                'name' => $regionData['region_name']
            ]);

            foreach($regionData['province_list'] as $provinceName => $provinceData){
                $province = Province::create([
                    'name' => $provinceName,
                    'region_id' => $region->code
                ]);

                foreach($provinceData['municipality_list'] as $cityName => $cityData){
                    $city = City::create([
                        'name' => $cityName,
                        'province_id' => $province->code
                    ]);


                    foreach($cityData['barangay_list'] as $brgyName){
                        $brgy = Barangay::create([
                            'name' => $brgyName,
                            'city_id' => $city->code
                        ]);

                    }
                }

            }

        }


    }
}
