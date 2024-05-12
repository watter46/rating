<?php declare(strict_types=1);

namespace App\UseCases\User\Fixture;

use Exception;
use Illuminate\Pagination\Paginator;

use App\Models\FixtureInfo;
use App\Models\TournamentType;


final readonly class FetchFixtures
{
    public function execute(TournamentType $tournament): Paginator
    {
        try {            
            /** @var Paginator $fixtureInfos */
            $fixtureInfos = FixtureInfo::query()
                ->with('fixture.players')
                ->selectWithout(['lineups'])
                ->whereNotNull('lineups')
                ->tournament($tournament)
                ->inSeasonTournament()
                ->currentSeason()
                ->finished()
                ->untilToday()
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