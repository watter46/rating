<?php declare(strict_types=1);

namespace App\UseCases\User\Fixture;

use Exception;
use Illuminate\Pagination\Paginator;

use App\Models\FixtureInfo;
use App\Models\TournamentType;


final readonly class FetchFixtures
{
    public function execute(TournamentType $tournament, $page = 1): Paginator
    {
        try {
            /** @var Paginator $fixtureInfos */
            $fixtureInfos = FixtureInfo::query()
                ->with('fixture.players')
                ->selectWithout([
                    'external_fixture_id',
                    'external_league_id',
                    'date',
                    'status',
                    'season',
                    'lineups'
                ])
                ->whereNotNull('lineups')
                // ->tournament($tournament)
                // ->finished()
                ->orderBy('date', 'asc')
                ->currentSeason()
                // ->withinOneMonth()
                // ->untilToday()
                ->simplePaginate();
                
            $fixtureInfos->getCollection()
                ->transform(function (FixtureInfo $fixtureInfo) {
                    $fixtureInfo->isRate = !is_null($fixtureInfo?->getRelation('fixture')?->players);
                    
                    return $fixtureInfo;
                });
                                
            return $fixtureInfos;

        } catch (Exception $e) {
            throw $e;
        }
    }
}