<?php declare(strict_types=1);

namespace Database\Seeders\Tests\User;

use Illuminate\Database\Seeder;

use App\Models\FixtureInfo;
use App\Models\User;

class TestingFixtureInfoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1035480 utd

        $external_fixture_id = 1035480;

        $testData = new TestDataGenerator();
                
        FixtureInfo::factory()
            ->fromFile($testData->getFixtureInfo($external_fixture_id))
            ->create()
            ->playerInfos()
            ->saveMany($testData->getPlayerInfos($external_fixture_id));
    }
}
