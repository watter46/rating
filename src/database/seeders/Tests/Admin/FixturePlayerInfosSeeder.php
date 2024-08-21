<?php declare(strict_types=1);

namespace Database\Seeders\Tests\Admin;

use App\Http\Controllers\Util\TestFixtureInfoFile;
use Illuminate\Database\Seeder;

use App\Http\Controllers\Util\TestPlayerInfoFile;
use App\Models\FixtureInfo;
use App\Models\PlayerInfo;
use Illuminate\Support\Carbon;

class FixturePlayerInfosSeeder extends Seeder
{    
    /**
     * FixtureInfoとPlyerInfoを保存する
     *
     * @return void
     */
    public function run(): void
    {        
        // 1035480 utd
        $external_fixture_id = 1035480;
                
        FixtureInfo::factory()
            ->fromFile((new TestFixtureInfoFile)->get($external_fixture_id))
            ->notStarted()
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