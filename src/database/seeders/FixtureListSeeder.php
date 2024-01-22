<?php declare(strict_types=1);

namespace Database\Seeders;

use App\Http\Controllers\Util\FixturesFile;
use App\Models\Fixture;
use App\UseCases\Player\Builder\FixtureDataListBuilder;
use Database\Mocks\Fixture\MockRegisterFixtureListUseCase;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FixtureListSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        /** @var MockRegisterFixtureListUseCase $mock */
        $mock = app(MockRegisterFixtureListUseCase::class);

        $mock->execute();
    }
}
