<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $adminRole = DB::table('roles')
            ->where('role_name', 'Admin')
            ->first();

        $staffRole = DB::table('roles')
            ->where('role_name', 'Staff')
            ->first();

        $adminInfo = DB::table('user_informations')
            ->where('first_name', 'Filemon')
            ->where('last_name', 'Galanida')
            ->first();

        $staffInfo = DB::table('user_informations')
            ->where('first_name', 'Je-ann')
            ->where('last_name', 'Callo')
            ->first();

        $adminAccount = DB::table('user_accounts')
            ->where('email', 'filemon@biringancity.gov.ph')
            ->first();

        $staffAccount = DB::table('user_accounts')
            ->where('email', 'je-ann@biringancity.gov.ph')
            ->first();

        DB::table('users')->insert([
            [
                'staff_ref_num' => '202656581024',
                'role_id' => $adminRole->role_id,
                'user_info_id' => $adminInfo->user_info_id,
                'user_acc_id' => $adminAccount->user_acc_id,
                'status' => 'Active',
            ],
            [
                'staff_ref_num' => '202656581025',
                'role_id' => $staffRole->role_id,
                'user_info_id' => $staffInfo->user_info_id,
                'user_acc_id' => $staffAccount->user_acc_id,
                'status' => 'Active',
            ],
        ]);
    }
}
