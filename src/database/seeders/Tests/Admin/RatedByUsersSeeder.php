<?php declare(strict_types=1);

namespace Database\Seeders\Tests\Admin;

use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;

use App\Http\Controllers\Util\TestFixtureInfoFile;
use App\Http\Controllers\Util\TestPlayerInfoFile;
use App\Models\Fixture;
use App\Models\FixtureInfo;
use App\Models\Player;
use App\Models\PlayerInfo;
use App\Models\User;


class RatedByUsersSeeder extends Seeder
{
    /**
     * User: 10
     * FixtureInfo: 1
     * PlayerInfo: 16
     * UsersPlayerStatistics: 16
     * Fixture: 10
     * Player: 50
     */
    public function run(): void
    {
        // 追加で4User作成(合計: 10)
        User::factory(9)->create();

        // 1035480 vs UTD
        $external_fixture_id = 1035480;
        
        // FixtureInfo(1)を作成
        $fixtureInfo = FixtureInfo::factory()
            ->fromFile((new TestFixtureInfoFile)->get($external_fixture_id))
            ->subDays(1)
            ->create();

        // PlayerInfo(16)を作成 UsersPlayerStatistics(16)を作成
        $playerInfos = $fixtureInfo
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
        
        // Fixture(5)を作成
        $fixtureInfoId = $fixtureInfo->id;
        
        $testFixtureData = User::pluck('id')
            ->map(function (int $userId) use ($fixtureInfoId) {
                return [
                    'mom_count' => 0,
                    'user_id' => $userId,
                    'fixture_info_id' => $fixtureInfoId
                ];
            });

        Fixture::upsert($testFixtureData->toArray(), ['id']);

        /**
         * Indexごとの平均
         * 6.3 7.0 7.3 7.0 7.9
         */
        $ratings = collect([
            [4.5, 6.2, 7.8, 5.9, 8.3],
            [5.1, 7.3, 6.8, 9.0, 4.7],
            [8.2, 5.6, 7.4, 6.9, 9.1],
            [6.3, 8.7, 5.2, 7.1, 9.5],
            [4.8, 6.5, 8.9, 7.2, 5.7],
            [7.6, 5.3, 9.2, 6.7, 8.1],
            [5.9, 7.8, 6.1, 8.5, 4.9],
            [8.8, 6.4, 7.5, 5.0, 9.3],
            [5.4, 7.9, 6.6, 8.2, 9.7],
            [6.0, 8.6, 7.0, 5.5, 9.4] 
        ]);

        $playerInfoIds = $playerInfos->take(5)->pluck('id');
        $momIndexes = collect([2, 1, 3, 0, 4, 2, 3, 1, 2, 0]);  // 最頻値のIndex: 2
        
        $testPlayerData = Fixture::pluck('id')
            ->map(function (string $fixtureId, $arrayIndex) use ($playerInfoIds, $ratings) {
                return $playerInfoIds
                    ->map(function (string $playerInfoId, $index) use ($fixtureId, $arrayIndex, $ratings) {
                        return [
                            'rating' => $ratings->dataGet("$arrayIndex".'.'."$index", false),
                            'mom' => false,
                            'rate_count' => 1,
                            'fixture_id' => $fixtureId,
                            'player_info_id' => $playerInfoId
                        ];
                    });
            })
            ->map(function (Collection $players, $index) use ($momIndexes) {
                $momIndex = $momIndexes[$index];
                
                return $players
                    ->map(function (array $player, $index) use ($momIndex) {
                        if ($index !== $momIndex) {
                            return $player;
                        }

                        $player['mom'] = true;
                
                        return $player;
                    });
            })
            ->flatten(1);

        Player::upsert($testPlayerData->toArray(), ['id']);
    }
}
