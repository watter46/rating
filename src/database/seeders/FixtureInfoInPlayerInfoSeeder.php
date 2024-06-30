<?php declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;

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

        $playerInfos = collect(
                PlayerInfo::query()
                    ->select(['id', 'api_football_id'])
                    ->get()
                    ->toArray()
            );

        $fixtureInfos
            ->each(function (FixtureInfo $fixtureInfo) use ($playerInfos) {
                $apiFootballIds = $fixtureInfo->lineups->flatten(1)->pluck('id');

                $playerInfoIds = $playerInfos->whereIn('api_football_id', $apiFootballIds)->pluck('id');
                
                $fixtureInfo->playerInfos()->attach($playerInfoIds);
            });
    }
}
