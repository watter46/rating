<?php declare(strict_types=1);

namespace App\UseCases\Fixture;

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
            return $this->fixture
                ->select(['id', 'score', 'date', 'external_fixture_id'])
                ->where('season', $this->season->current())
                ->whereDate('date', '<=', now())
                ->orderBy('date', 'desc')
                ->paginate(20);

        } catch (ModelNotFoundException $e) {
            throw new ModelNotFoundException('プロジェクトが見つかりませんでした。');

        } catch (Exception $e) {
            throw $e;
        }
    }
}