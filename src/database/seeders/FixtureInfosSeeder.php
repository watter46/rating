<?php declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;

use App\Infrastructure\ApiFootball\MockApiFootballRepository;
use App\Models\FixtureInfo;
use App\UseCases\Admin\Fixture\Data\FixtureStatusType;


class FixtureInfosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        /** @var MockApiFootballRepository $repository */
        $repository = app(MockApiFootballRepository::class);

        $fixtureInfosData = $repository->fetchFixtures();

        $data = $fixtureInfosData
            ->build()
            ->filter(function (Collection $fixtureInfo) use ($repository) {
                $data = $repository->fetchFixture($fixtureInfo['external_fixture_id']);

                return $data->isSeasonTournament();
            })
            ->map(function (Collection $fixtureInfo) use ($repository) {
                $data = $repository->fetchFixture($fixtureInfo['external_fixture_id']);
                
                if (!$data->isSeasonTournament()) {
                    return $fixtureInfo;
                }
                
                if (!$data->isFinished()) {
                    return $fixtureInfo;
                }

                $fixtureInfo['lineups'] = $data->buildLineups()->get('lineups')->toJson();
                $fixtureInfo['score']   = $data->buildScore()->toJson();
                $fixtureInfo['fixture'] = $data->buildFixture()->toJson();
                $fixtureInfo['status']  = FixtureStatusType::MatchFinished->value;

                return $fixtureInfo;
            });

        FixtureInfo::upsert($data->toArray(), FixtureInfo::UPSERT_UNIQUE);
    }
}
