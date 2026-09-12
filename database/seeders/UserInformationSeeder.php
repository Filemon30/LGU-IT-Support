<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserInformationSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('user_informations')->insert([
            [
                'first_name' => 'Filemon',
                'last_name' => 'Galanida',
                'middle_name' => 'Leornas',
                'suffix' => 'Jr.',
                'gender' => 'Male',
                'birth_date' => '2001-12-30',
                'barangay' => 'Barangay 1 Springside',
                'city' => 'Malaybalay City',
                'province' => 'Bukidnon',
                'contact_number' => '09123456789',
            ],
            [
                'first_name' => 'Je-ann',
                'last_name' => 'Callo',
                'middle_name' => null,
                'suffix' => null,
                'gender' => 'Female',
                'birth_date' => '2005-02-17',
                'barangay' => 'Barangay Casisang',
                'city' => 'Malaybalay City',
                'province' => 'Bukidnon',
                'contact_number' => '09987654321',
            ],
        ]);
    }
}
