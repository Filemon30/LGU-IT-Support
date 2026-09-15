<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PriorityLevelSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('priority_levels')->insert([
            [
                'priority_level_id' => 1,
                'priority_name' => 'Critical',
                'priority_description' => 'Critical issues that require immediate attention because they severely affect operations or essential services.',
                'response_target_minutes' => 15,
                'resolution_target_minutes' => 120,
            ],
            [
                'priority_level_id' => 2,
                'priority_name' => 'High',
                'priority_description' => 'High-impact issues that significantly affect users or important services and require prompt attention.',
                'response_target_minutes' => 30,
                'resolution_target_minutes' => 240,
            ],
            [
                'priority_level_id' => 3,
                'priority_name' => 'Medium',
                'priority_description' => 'Issues that affect normal operations but have an available workaround or limited impact.',
                'response_target_minutes' => 60,
                'resolution_target_minutes' => 480,
            ],
            [
                'priority_level_id' => 4,
                'priority_name' => 'Low',
                'priority_description' => 'Minor issues or requests that have minimal impact on operations and can be handled during normal support activities.',
                'response_target_minutes' => 120,
                'resolution_target_minutes' => 1440,
            ],
        ]);
    }
}