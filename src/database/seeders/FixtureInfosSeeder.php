<?php declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;

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
            ->map(function (Collection $fixtureInfo) use ($repository) {                
                $fixtureData = $repository->fetchFixture($fixtureInfo['external_fixture_id']);
                                                
                if (!$fixtureData->lineupsExists()) {
                    $fixtureInfo['lineups'] = null;

                    return $fixtureInfo;
                }
                
                $fixtureInfo['lineups'] = $fixtureData->getLineups()->toJson();

                return $fixtureInfo;
            });

        FixtureInfo::upsert($data->toArray(), FixtureInfo::UPSERT_UNIQUE);
    }
}