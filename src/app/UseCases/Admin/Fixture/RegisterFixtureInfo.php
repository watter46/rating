<?php declare(strict_types=1);

namespace App\UseCases\Admin\Fixture;

use Exception;
use Illuminate\Support\Facades\DB;

use App\Models\FixtureInfo as FixtureInfoModel;
use App\UseCases\Admin\ApiFootballRepositoryInterface;


final readonly class RegisterFixtureInfo
{
    public function __construct(private ApiFootballRepositoryInterface $repository) {
        //
    }

    public function execute(string $fixtureInfoId): FixtureInfoModel
    {
        try {
            /** @var FixtureInfoModel $model */
            $model = FixtureInfoModel::with('playerInfos')
                ->selectWithout(['lineups'])
                ->findOrFail($fixtureInfoId);
            
            $fixtureInfo = $this->repository
                ->preFetchFixture($model->api_fixture_id)
                ->updatePlayerInfos();

            $newModel = $model->fill($fixtureInfo->buildFill());
            
            DB::transaction(function () use ($newModel) {
                $newModel->save();
            });
            
            if ($fixtureInfo->shouldDispatch()) {
                $fixtureInfo
                    ->assignId($newModel->id)
                    ->dispatch();
            }

            return $newModel;
 
        } catch (Exception $e) {
            throw $e;
        }
    }
}