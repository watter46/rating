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
        $this->call([
            FixtureInfosSeeder::class,
            PlayerInfoSeeder::class,
            FixtureInfoInPlayerInfoSeeder::class,
            UserSeeder::class,
            AdminSeeder::class
        ]);
    }
}