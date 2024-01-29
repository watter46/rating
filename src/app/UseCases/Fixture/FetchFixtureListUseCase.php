<?php declare(strict_types=1);

namespace App\UseCases\Fixture;

use Exception;
use Illuminate\Pagination\Paginator;

use App\Models\Fixture;
use App\Http\Controllers\TournamentType;


final readonly class FetchFixtureListUseCase
{    
    public function __construct()
    {
        //
    }

    public function execute(TournamentType $tournament): Paginator
    {
        try {
            /** @var Paginator $fixture */
            $fixture = Fixture::query()
                ->with('players:fixture_id')
                ->past()
                ->inSeason()
                ->tournament($tournament)
                ->simplePaginate(20);

            $fixture->getCollection()
                ->transform(function (Fixture $model) {
                    $model->dataExists = !is_null($model->fixture);

                    $model->isEvaluate = $model->players->isNotEmpty();

                    unset($model->fixture);

                    return $model;
                });

            return $fixture;

        } catch (Exception $e) {
            throw $e;
        }
    }
}