<?php declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

use App\Models\FixtureInfo;
use App\Models\PlayerInfo;


class FixtureInfoInPlayerInfoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $fixtureInfos = FixtureInfo::get(['id', 'lineups']);

        $playerInfoMap = PlayerInfo::query()
            ->select(['id', 'api_player_id'])
            ->get()
            ->keyBy('api_player_id')
            ->map(fn(PlayerInfo $playerInfo) => $playerInfo->id);
        
        $lineupsExistFixtureInfos = $fixtureInfos
            ->filter(function (FixtureInfo $fixtureInfo) {
                return $fixtureInfo->lineups;
            });

        if ($lineupsExistFixtureInfos->isEmpty()) return;
            
        $insertData = $lineupsExistFixtureInfos
            ->map(function (FixtureInfo $fixtureInfo) use ($playerInfoMap) {
                return $fixtureInfo->lineups
                    ->flatten(1)
                    ->pluck('id')
                    ->map(function (int $api_player_id) use ($fixtureInfo, $playerInfoMap) {                        
                        return [
                            'id' => Str::ulid(),
                            'fixture_info_id' => $fixtureInfo->id,
                            'player_info_id'  => $playerInfoMap[$api_player_id]
                        ];
                    });
            })
            ->flatten(1);

        $this->bulkInsert($insertData->toArray());
    }

    private function bulkInsert(array $data)
    {
        DB::table('users_player_ratings')->insert($data);
    }
}
