<?php declare(strict_types=1);

namespace App\UseCases\User\Fixture;

use Exception;
use Illuminate\Pagination\Paginator;

use App\Models\Fixture;
use App\Models\TournamentType;


final readonly class FetchFixturesUseCase
{
    public function execute(TournamentType $tournament): Paginator
    {
        try {            
            /** @var Paginator $fixtures */
            $fixtures = Fixture::query()
                ->with('players:id')
                ->select(['id', 'fixture'])
                ->whereNotNull('fixture')
                ->currentSeason()
                ->tournament($tournament)
                ->finished()
                ->where('date', '<=', now('UTC'))
                ->orderBy('date', 'desc')
                ->simplePaginate(20);

            $fixtures->getCollection()
                ->transform(function (Fixture $fixture) {
                    $fixture->isRate = $fixture->players->isNotEmpty();
                    
                    return $fixture;
                });
                
            return $fixtures;

        } catch (Exception $e) {
            throw $e;
        }
    }
}