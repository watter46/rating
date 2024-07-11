<?php declare(strict_types=1);

namespace App\UseCases\Admin\Fixture;

use Exception;
use Illuminate\Support\Facades\DB;

use App\Models\FixtureInfo;
use App\UseCases\Admin\ApiFootballRepositoryInterface;


class RegisterFixtureInfos
{
    public function __construct(
        private FixtureInfo $fixtureInfo,
        private ApiFootballRepositoryInterface $apiFootballRepository)
    {
        //
    }

    public function execute(): void
    {
        try {
            $fixturesInfosData = $this->apiFootballRepository->fetchFixtures();

            $data = $this->fixtureInfo
                ->fixtureInfosBuilder()
                ->bulkUpdate($fixturesInfosData);

            DB::transaction(function () use ($data) {
                FixtureInfo::upsert($data->toArray(), FixtureInfo::UPSERT_UNIQUE);
            });

            $this->fixtureInfo->fixtureInfosBuilder()->dispatch();

        } catch (Exception $e) {
            throw $e;
        }
    }
}