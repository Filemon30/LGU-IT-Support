<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserAccountSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('user_accounts')->insert([
            [
                'email' => 'filemon@biringancity.gov.ph',
                'password_hash' => Hash::make('123'),
                'last_login_at' => null,
            ],
            [
                'email' => 'je-ann@biringancity.gov.ph',
                'password_hash' => Hash::make('123'),
                'last_login_at' => null,
            ],
        ]);
    }
}
