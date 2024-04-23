<?php declare(strict_types=1);

namespace App\UseCases\Admin\Fixture;

use Exception;
use Illuminate\Pagination\Paginator;

use App\Models\Fixture;

final readonly class FetchFixturesUseCase
{
    public function execute(): Paginator
    {
        try {
            // /** @var Paginator $fixtures */
            // $fixtures = Fixture::query()
            //     ->with('players:fixture_id')
            //     ->past()
            //     ->inSeason()
            //     ->simplePaginate(20);

            // $fixtures->getCollection()
            //     ->transform(function (Fixture $model) {
            //         $model->dataExists = !is_null($model->fixture);

            //         $model->isRate = $model->players->isNotEmpty();

            //         unset($model->fixture);

            //         return $model;
            //     });
                
            // return $fixtures;

            /** @var Paginator $fixtures */
            $fixtures = Fixture::query()
                ->with('players:id')
                ->inSeasonTournament()
                ->currentSeason()
                ->finished()
                ->untilToday()
                ->simplePaginate();

            $fixtures->getCollection()
                ->transform(function (Fixture $fixture) {
                    $fixture->dataExists = !is_null($fixture->fixture);
                    $fixture->isRate = $fixture->players->isNotEmpty();
                    
                    return $fixture;
                });

            return $fixtures;

        } catch (Exception $e) {
            throw $e;
        }
    }
}