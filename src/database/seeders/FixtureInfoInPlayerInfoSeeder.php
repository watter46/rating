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
            ->select(['id', 'api_football_id'])
            ->get()
            ->keyBy('api_football_id')
            ->map(fn(PlayerInfo $playerInfo) => $playerInfo->id);
            
        $insertData = $fixtureInfos
            ->map(function (FixtureInfo $fixtureInfo) use ($playerInfoMap) {
                return $fixtureInfo->lineups
                    ->flatten(1)
                    ->pluck('id')
                    ->map(function (int $apiFootballId) use ($fixtureInfo, $playerInfoMap) {
                        return [
                            'id' => Str::ulid(),
                            'fixture_info_id' => $fixtureInfo->id,
                            'player_info_id'  => $playerInfoMap[$apiFootballId]
                        ];
                    });
            })
            ->flatten(1);

        $this->bulkInsert($insertData->toArray());
    }

    private function bulkInsert(array $data)
    {
        DB::table('users_player_statistics')->insert($data);
    }
}
