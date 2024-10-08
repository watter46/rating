<?php declare(strict_types=1);

namespace App\UseCases\Admin\Fixture;

use App\Http\Controllers\Util\FixtureFile;
use App\Http\Controllers\Util\FixtureInfoFile;
use App\Http\Controllers\Util\FixturesFile;
use App\Http\Controllers\Util\PlayerInfoFile;
use App\Http\Controllers\Util\TestFixtureInfoFile;
use App\Http\Controllers\Util\TestFixtureInfosFile;
use App\Infrastructure\ApiFootball\MockApiFootballRepository;
use Exception;
use Illuminate\Support\Facades\DB;

use App\Models\FixtureInfo as FixtureInfoModel;
use App\Models\PlayerInfo;
use App\UseCases\Admin\ApiFootballRepositoryInterface;
use App\UseCases\Admin\Data\ApiFootball\FixtureData\FixtureStatusType;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;

class RegisterFixtureInfos
{
    public function __construct(
        private ApiFootballRepositoryInterface $apiFootballRepository)
    {
        //
    }

    public function execute(): void
    {
        try {
            /** @var Collection<FixtureInfoModel> $models */
            $models = FixtureInfoModel::query()
                ->currentSeason()
                ->get(['id', 'api_fixture_id'])
                ->toCollection();

            $fixtureInfos = $this->apiFootballRepository
                ->fetchFixtures()
                ->bulkUpdate($models);

            DB::transaction(function () use ($fixtureInfos) {
                FixtureInfoModel::upsert(
                    $fixtureInfos->toArray(),
                    FixtureInfoModel::UPSERT_UNIQUE,
                    FixtureInfoModel::UPSERT_COLUMNS
                );
            });

            if ($fixtureInfos->shouldDispatch()) {                
                $fixtureInfos->dispatch();
            }

        } catch (Exception $e) {
            throw $e;
        }
    }
}