<?php declare(strict_types=1);

namespace App\UseCases\Fixture;

use Exception;
use Illuminate\Pagination\Paginator;

use App\Models\Fixture;
use App\Http\Controllers\TournamentType;


final readonly class FetchFixturesUseCase
{
    public function execute(TournamentType $tournament): Paginator
    {
        try {            
            /** @var Paginator $fixtures */
            $fixtures = Fixture::query()
                ->with('players:fixture_id')
                ->whereNotNull('fixture')
                ->finished()
                ->past()
                ->inSeason()
                ->tournament($tournament)
                ->simplePaginate(20);

            $fixtures->getCollection()
                ->transform(function (Fixture $model) {
                    $model->dataExists = !is_null($model->fixture);

                    $model->isRate = $model->players->isNotEmpty();

                    unset($model->fixture);

                    return $model;
                });
                
            return $fixtures;

        } catch (Exception $e) {
            throw $e;
        }
    }
}