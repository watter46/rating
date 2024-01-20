<?php declare(strict_types=1);

namespace App\UseCases\Fixture;

use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Pagination\LengthAwarePaginator;

use App\Http\Controllers\Util\FixtureFile;
use App\Http\Controllers\Util\FixturesFile;
use App\Models\Fixture;
use App\UseCases\Util\Season;


final readonly class FetchFixtureListUseCase
{    
    public function __construct(
        private Fixture $fixture,
        private Season $season,
        private FixtureFile $file,
        private FixturesFile $fixtures)
    {
        //
    }

    public function execute(): LengthAwarePaginator
    {
        try {            
            /** @var LengthAwarePaginator $fixture */
            $fixture = $this->fixture
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

        } catch (ModelNotFoundException $e) {
            throw new ModelNotFoundException('プロジェクトが見つかりませんでした。');

        } catch (Exception $e) {
            throw $e;
        }
    }
}