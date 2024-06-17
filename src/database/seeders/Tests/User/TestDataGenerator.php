<?php

namespace Database\Seeders\Tests\User;

use App\Models\PlayerInfo;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;


class TestDataGenerator
{
    // $fixtureInfos = FixtureInfo::whereIn('external_fixture_id', [1035505, 1035528, 1035515, 1035454, 1035480])
    //             ->get();

    //         foreach($fixtureInfos as $fixtureInfo) {
    //             File::put(app_path('Template/tests/'.$fixtureInfo->external_fixture_id.'.json'), $fixtureInfo->toJson());
    //         }

    public function writePlayerInfos(int $external_fixture_id)
    {
        $playerInfos = PlayerInfo::query()
            ->whereIn('foot_player_id', collect($playerInfo->lineups)
                ->flatten(1)
                ->pluck('id')
                ->toArray())
            ->get();

            File::put(app_path('Template/tests/playerInfos/1035480.json'), $playerInfos->toJson());
    }

    public function getFixtureInfo(int $external_fixture_id)
    {
        return json_decode(File::get(app_path('Template/tests/fixtures/'.$external_fixture_id.'.json')));
    }

    public function getPlayerInfos(int $external_fixture_id): Collection
    {
        $data = File::get(app_path('Template/tests/playerInfos/'.$external_fixture_id.'.json'));
        
        return collect(json_decode($data))
            ->map(function ($player) {
                return PlayerInfo::factory()
                    ->fromFile($player)
                    ->make();
            });
    }
}
