<?php declare(strict_types=1);

namespace App\UseCases\User\Fixture;

use App\Http\Controllers\Util\TestFixtureInfoFile;
use App\Http\Controllers\Util\TestOneItemFile;
use App\Http\Controllers\Util\TestPlayerInfoFile;
use Exception;
use Illuminate\Pagination\Paginator;

use App\Models\FixtureInfo;
use App\Models\TournamentType;


final readonly class FetchFixtures
{
    public function execute(TournamentType $tournament, $page = 1): Paginator
    {
        try {
            // (new TestFixtureInfoFile)->writeAll();
            // (new TestPlayerInfoFile)->writeAll();
            // (new TestOneItemFile)->write();
            
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