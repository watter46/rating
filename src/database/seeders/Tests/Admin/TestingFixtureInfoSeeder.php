<?php declare(strict_types=1);

namespace Database\Seeders\Tests\Admin;

use Illuminate\Database\Seeder;

use App\Http\Controllers\Util\TestFixtureInfoFile;
use App\Http\Controllers\Util\TestPlayerInfoFile;
use App\Models\FixtureInfo;
use App\Models\PlayerInfo;


class TestingFixtureInfoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1035480 utd

        $api_fixture_id = 1035480;
                
        FixtureInfo::factory()
            ->fromFile((new TestFixtureInfoFile)->get($api_fixture_id))
            ->notStarted()
            ->create()
            ->playerInfos()
            ->saveMany(
                (new TestPlayerInfoFile)
                    ->get($api_fixture_id)
                    ->map(function ($player) {
                        return PlayerInfo::factory()
                            ->fromFile($player)
                            ->make();
                    })
            );
    }
}
