<?php declare(strict_types=1);

namespace App\UseCases\Fixture;

use Exception;
use Illuminate\Pagination\Paginator;

use App\Models\Fixture;
use App\Http\Controllers\TournamentType;
use App\UseCases\Admin\Fixture\RegisterFixturesUseCase;

final readonly class FetchFixturesUseCase
{
    public function __construct(private RegisterFixturesUseCase $f)
    {
        
    }
    public function execute(TournamentType $tournament): Paginator
    {
        try {
            $this->f->execute();
            
            /** @var Paginator $fixtures */
            $fixtures = Fixture::query()
                ->with('players:fixture_id')
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