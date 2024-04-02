<?php declare(strict_types=1);

namespace App\UseCases\Admin\Fixture\FixturesData;

use Illuminate\Support\Collection;

use App\Models\Fixture;


final readonly class FixturesDataBuilder
{
    public function __construct()
    {
        //
    }
    
    /**
     * build
     *
     * @param  FixturesData $fixturesData
     * @return Collection
     */
    public function build(FixturesData $fixturesData): Collection
    {
        /** @var Collection<int, Fixture> */
        $fixtures = Fixture::query()
            ->select(['id', 'external_fixture_id'])
            ->currentSeason()
            ->get();

        $result = $fixtures->isNotEmpty()
            ? $fixturesData->getData()
                ->map(function (FixturesDetailData $fixtureDetailData) use ($fixtures) {
                    $fixture = $fixtures
                        ->keyBy('external_fixture_id')
                        ->get($fixtureDetailData->getFixtureId());

                    if (!$fixture) {
                        return $fixtureDetailData->build();
                    }
                    
                    return $fixtureDetailData->build()->merge($fixture);
                })
            : $fixturesData->format();

        return $result;
    }
}