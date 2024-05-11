<?php declare(strict_types=1);

namespace Database\Stubs\Fixture;

use Exception;

use App\Models\FixtureInfo;
use Database\Stubs\Infrastructure\ApiFootball\MockApiFootballRepository;


class StubRegisterFixtureInfos
{
    public function execute(): void
    {
        try {
            /** @var MockApiFootballRepository $repository */
            $repository = app(MockApiFootballRepository::class);

            $fixtureInfosData = $repository->fetchFixtures();
            
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

            FixtureInfo::upsert($fixtureInfosData->build()->toArray(), $unique, $updateColumns);

        } catch (Exception $e) {
            throw $e;
        }
    }
}