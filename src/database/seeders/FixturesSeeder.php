<?php declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;

use Database\Stubs\Fixture\StubRegisterFixturesUseCase;

class FixturesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        /** @var StubRegisterFixturesUseCase $registerFixtures */
        $registerFixtures = app(StubRegisterFixturesUseCase::class);

        $registerFixtures->execute();
    }
}
