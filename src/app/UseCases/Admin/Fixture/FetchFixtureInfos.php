<?php declare(strict_types=1);

namespace App\UseCases\Admin\Fixture;

use Exception;
use Illuminate\Pagination\Paginator;

use App\Models\FixtureInfo;


final readonly class FetchFixtureInfos
{
    public function __construct(private RegisterFixtureInfo $registerFixtureInfo)
    {
        
    }
    public function execute(): Paginator
    {
        try {            
            /** @var Paginator $fixtureInfos */
            $fixtureInfos = FixtureInfo::query()
                ->selectWithout([
                    'external_fixture_id',
                    'external_league_id',
                    'date',
                    'status',
                    'season',
                ])
                ->inSeasonTournament()
                ->currentSeason()
                ->untilToday()
                ->simplePaginate();

            $fixtureInfos->getCollection()
                ->transform(function (FixtureInfo $fixture) {
                    $fixture->lineupsExists = !is_null($fixture->lineups);

                    unset($fixture->lineups);
                    
                    return $fixture;
                });

            return $fixtureInfos;

        } catch (Exception $e) {
            throw $e;
        }
    }
}