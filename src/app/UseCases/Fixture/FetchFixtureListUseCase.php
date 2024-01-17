<?php declare(strict_types=1);

namespace App\UseCases\Fixture;

use App\Http\Controllers\Util\FixtureFile;
use App\Models\Fixture;
use App\UseCases\Util\Season;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Pagination\LengthAwarePaginator;

final readonly class FetchFixtureListUseCase
{
    public function __construct(private Fixture $fixture, private Season $season)
    {
        //
    }

    public function execute(): LengthAwarePaginator
    {
        try {            
            /** @var LengthAwarePaginator $fixture */
            $fixture = $this->fixture
                ->select(['id', 'score', 'date', 'external_fixture_id', 'fixture'])
                ->where('season', $this->season->current())
                ->whereDate('date', '<=', now())
                ->orderBy('date', 'desc')
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