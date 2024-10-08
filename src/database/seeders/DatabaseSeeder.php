<?php declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;


class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        config(['seeder.status' => true]);
                
        $this->call([
            // FixtureInfosSeeder::class,
            // PlayerInfoSeeder::class,
            // FixtureInfoSeeder::class,
            // FixturePlayerInfosSeeder::class,
            // FixtureInfoInPlayerInfoSeeder::class,

            FixtureInfoSeeder::class,
            UserSeeder::class,
            AdminSeeder::class
        ]);

        config(['seeder.status' => false]);
    }
}