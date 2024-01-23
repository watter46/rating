<?php declare(strict_types=1);

namespace App\UseCases\Fixture;

use Exception;
use Illuminate\Pagination\LengthAwarePaginator;

use App\Models\Fixture;


final readonly class FetchFixtureListUseCase
{    
    public function __construct()
    {
        //
    }

    public function execute(): LengthAwarePaginator
    {
        try {
            /** @var LengthAwarePaginator $fixture */
            $fixture = Fixture::query()
                ->past()
                ->inSeason()
                ->paginate(20);

            $fixture->getCollection()
                ->transform(function (Fixture $model) {
                    $model->dataExists = !is_null($model->fixture);

                    unset($model->fixture);

                    return $model;
                });

            return $fixture;

        } catch (Exception $e) {
            throw $e;
        }
    }
}