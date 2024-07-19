<?php declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;

use App\Models\FixtureInfo;
use App\Infrastructure\ApiFootball\MockApiFootballRepository;


class FixtureInfosSeeder extends Seeder
{
    public function run(): void
    {
        /** @var MockApiFootballRepository $repository */
        $repository = app(MockApiFootballRepository::class);

        $fixturesData = $repository->fetchFixtures();

        $data = (new FixtureInfo)
            ->fixtureInfosBuilder()
            ->bulkUpdate($fixturesData)
            ->map(function (FixtureInfo $fixtureInfo) use ($repository) {                
                $fixtureData = $repository->fetchFixture($fixtureInfo->external_fixture_id);
                
                return $fixtureInfo
                    ->fixtureInfoBuilder()
                    ->update($fixtureData)
                    ->castsToJson();
            });

        FixtureInfo::upsert($data->toArray(), FixtureInfo::UPSERT_UNIQUE);
    }
}