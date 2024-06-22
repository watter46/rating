<?php declare(strict_types=1);

namespace Database\Seeders\Tests\User;

use Illuminate\Database\Seeder;

use App\Models\FixtureInfo;
use App\Models\PlayerInfo;
use App\Http\Controllers\Util\TestFixtureInfoFile;
use App\Http\Controllers\Util\TestPlayerInfoFile;


class TestingFixtureInfoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1035480 utd

        $external_fixture_id = 1035480;
                
        FixtureInfo::factory()
            ->fromFile((new TestFixtureInfoFile)->get($external_fixture_id))
            ->create()
            ->playerInfos()
            ->saveMany(
                (new TestPlayerInfoFile)
                    ->get($external_fixture_id)
                    ->map(function ($player) {
                        return PlayerInfo::factory()
                            ->fromFile($player)
                            ->make();
                    })
            );
    }
}
