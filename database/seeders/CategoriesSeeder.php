<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategoriesSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('categories')->insert([
            [
                'category_id' => 1,
                'category_name' => 'Hardware',
                'description' => 'Issues related to computers, printers, monitors, peripherals, and other physical IT equipment.',
                'icon' => 'ti ti-device-desktop',
                'color_id' => 1,
                'status' => 'Active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'category_id' => 2,
                'category_name' => 'Software',
                'description' => 'Issues related to applications, operating systems, software installation, errors, and configuration.',
                'icon' => 'ti ti-apps',
                'color_id' => 2,
                'status' => 'Active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'category_id' => 3,
                'category_name' => 'Network',
                'description' => 'Issues related to internet connectivity, network access, Wi-Fi, LAN, and other network services.',
                'icon' => 'ti ti-wifi',
                'color_id' => 3,
                'status' => 'Active',
                'created_at' => now(),
                'updated_at' => now(),
            ],

             [
                'category_id' => 4,
                'category_name' => 'Others',
                'description' => 'Issues that do not fall under the categories of Hardware, Software, or Network, including miscellaneous IT-related problems.',
                'icon' => 'ti ti-folder',
                'color_id' => 6,
                'status' => 'Active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
