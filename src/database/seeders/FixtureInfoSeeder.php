<?php declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;

use App\Models\FixtureInfo;
use App\Infrastructure\ApiFootball\MockApiFootballRepository;
use App\Models\PlayerInfo;
use App\UseCases\Admin\Data\ApiFootball\FixtureData\FixtureData;
use App\UseCases\Util\Season;

class FixtureInfoSeeder extends Seeder
{
    public function run(): void
    {
        // 1035480 utd
        $external_fixture_id = 1035480;
        
        /** @var MockApiFootballRepository $repository */
        $repository = app(MockApiFootballRepository::class);

        $fixtureData = $repository->fetchFixture($external_fixture_id);

        $data = $fixtureData->getResultData();
        
        $fixtureInfo = new FixtureInfo([
                'external_fixture_id' => $data['fixtureId'],
                'external_league_id'  => $data['leagueId'],
                'season'              => Season::current(),
                'date'                => now('UTC'),
                'status'              => $data['status'],
                'score'               => $data['score'],
                'teams'               => $data['teams'],
                'league'              => $data['league'],
                'fixture'             => $data['fixture'],
                // 'lineups'             => $fixtureData->getLineups()
                'lineups'             => null
            ]);

        $fixtureInfo->save();

        $playerInfoIds = PlayerInfo::query()
            ->whereIn('api_football_id', $fixtureData->getPlayedPlayers()->pluck('id'))
            ->pluck('id');
        
        $fixtureInfo->playerInfos()->sync($playerInfoIds);
    }
}