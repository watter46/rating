<?php declare(strict_types=1);

namespace App\UseCases\Admin\Fixture;

use Exception;
use Illuminate\Support\Facades\DB;

use App\Models\FixtureInfo;
use App\UseCases\Admin\ApiFootballRepositoryInterface;


class RegisterFixtureInfos
{
    public function __construct(private ApiFootballRepositoryInterface $apiFootballRepository)
    {
        //
    }

    public function execute(): void
    {
        try {
            $fixturesInfosData = $this->apiFootballRepository->fetchFixtures();
                        
            DB::transaction(function () use ($fixturesInfosData) {
                $unique = ['id'];
                $updateColumns = [
                    'season',
                    'date',
                    'status',
                    'score',
                    'teams',
                    'league',
                    'fixture'
                ];

                FixtureInfo::upsert($fixturesInfosData->build()->toArray(), $unique, $updateColumns);
            });

            FixtureInfo::fixturesRegistered($fixturesInfosData);

        } catch (Exception $e) {
            throw $e;
        }
    }
}