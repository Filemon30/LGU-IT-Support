<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ColorSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('colors')->insert([
            [
                'color_id' => 1,
                'color_name' => 'orange',
                'color_value' => '#F97316',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'color_id' => 2,
                'color_name' => 'blue',
                'color_value' => '#3B82F6',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'color_id' => 3,
                'color_name' => 'green',
                'color_value' => '#22C55E',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'color_id' => 4,
                'color_name' => 'red',
                'color_value' => '#EF4444',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'color_id' => 5,
                'color_name' => 'purple',
                'color_value' => '#A855F7',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'color_id' => 6,
                'color_name' => 'yellow',
                'color_value' => '#EAB308',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
