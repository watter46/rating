<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Database\Seeders\StatisticSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            FixtureListSeeder::class,
            PlayerInfoSeeder::class,
            FixtureSeeder::class,
            // PlayerSeeder::class,
            UserSeeder::class,
            AdminSeeder::class
        ]);
    }
}
