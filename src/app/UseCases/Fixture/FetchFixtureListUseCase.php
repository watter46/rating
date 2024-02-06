<?php declare(strict_types=1);

namespace App\UseCases\Fixture;

use Exception;
use Illuminate\Pagination\Paginator;

use App\Models\Fixture;
use App\Http\Controllers\TournamentType;
use App\Models\Exceptions\FixtureNotFoundException;

final readonly class FetchFixtureListUseCase
{    
    public function __construct(private RegisterFixtureUseCase $registerFixture)
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
                
            if ($fixture->whereNull('fixture')->isNotEmpty()) {
                foreach($fixture->whereNull('fixture') as $fixture) {
                    $this->registerFixture->execute($fixture->id);
                }

                $this->execute($tournament);
            }
            
            $fixture->getCollection()
                ->transform(function (Fixture $model) {
                    $model->dataExists = !is_null($model->fixture);

                    $model->isRate = $model->players->isNotEmpty();

                    unset($model->fixture);

                    return $model;
                });

            return $fixture;
        
        } catch (Exception $e) {
            throw $e;
        }
    }
}