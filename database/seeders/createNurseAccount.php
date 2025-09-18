<?php

namespace Database\Seeders;

use App\Models\addresses;
use App\Models\nurses;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class createNurseAccount extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::create([
            'username'          => 'nurse01',
            'email'             => 'nurse01@example.com',
            'password'          => Hash::make('Password123!'),
            'recovery_question' => 1,
            'recovery_answer'   => Hash::make('nurse123'),
            'status'            => 'active',
            'role' => 'nurse'
        ]);

        $address = addresses::create([
            'user_id' => $user->id
        ]);

        $nurseInfo = nurses::create([
            'user_id'       => $user->id,
            'first_name'    => 'Jane',
            'middle_initial'=> 'D',
            'last_name'     => 'Doe',
            'full_name'     => 'Jane D Doe',
            'profile_image' => 'images/default_profile.png',
            'address_id' => $address->address_id
        ]);
    }
}
