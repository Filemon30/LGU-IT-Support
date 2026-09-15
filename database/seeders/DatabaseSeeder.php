<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            UserInformationSeeder::class,
            UserAccountSeeder::class,
            UserSeeder::class,
            ColorSeeder::class,
            CategoriesSeeder::class,
            PriorityLevelSeeder::class,
        ]);
    }
}
