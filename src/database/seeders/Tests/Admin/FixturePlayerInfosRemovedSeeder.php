<?php declare(strict_types=1);

namespace Database\Seeders\Tests\Admin;

use App\Http\Controllers\Util\TestFixtureInfoFile;
use Illuminate\Database\Seeder;

use App\Http\Controllers\Util\TestPlayerInfoFile;
use App\Models\FixtureInfo;
use App\Models\PlayerInfo;


class FixturePlayerInfosRemovedSeeder extends Seeder
{    
    /**
     * Lineupから1人(api_football_id: 116117, "Moisés Caicedo")を取り除いて
     * PlyerInfoを保存する
     *
     * @return void
     */
    public function run(): void
    {
        $api_fixture_id = 1035480;
                
        FixtureInfo::factory()
            ->fromFile((new TestFixtureInfoFile)->get($api_fixture_id))
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
                    ->reject(fn($player) => $player->api_player_id === 116117)
            );
    }
}