<?php declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\UseCases\Fixture\RegisterFixtureListUseCase;
use App\UseCases\Player\Builder\FixtureDataListBuilder;


class FixtureListSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        /** @var RegisterFixtureListUseCase $fixtures */
        $fixtures = app(RegisterFixtureListUseCase::class);

        $fixtures->execute();
    }
}
