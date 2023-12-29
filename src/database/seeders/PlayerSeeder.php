<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

use App\Models\PlayerInfo;
use App\Models\Fixture;
use App\Models\Player;


class PlayerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $fixtureId = 1035338;
        
        /** @var Fixture $fixture */
        $fixture = Fixture::where('external_fixture_id', $fixtureId)->first();

        $footPlayerIdList = collect($fixture->fixture['lineups'])
            ->dot()
            ->filter(function ($id, $key) {
                if (Str::afterLast($key, '.') === 'id') {
                    return $id;
                }
            })
            ->values()
            ->toArray();

        // Playerの評価を8.0にする
        $playerInfos = PlayerInfo::query()
            ->select(['id', 'foot_player_id'])
            ->whereIn('foot_player_id', $footPlayerIdList)
            ->get();
        
        $data = $playerInfos->map(function (PlayerInfo $playerInfo) use ($fixture) {
            $player = new Player(['rating' => 8.0]);

            $player->playerInfo()->associate($playerInfo);
            $player->fixture()->associate($fixture);

            return $player->getAttributes();
        })
        ->toArray();

        $unique = ['id'];
        $updateColumns = ['rating'];
        
        Player::upsert($data, $unique, $updateColumns);
    }
}
