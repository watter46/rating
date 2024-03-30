<?php declare(strict_types=1);

namespace App\UseCases\Admin\Fixture\FixturesData;

use Illuminate\Support\Collection;

use App\Models\Fixture;
use App\UseCases\Admin\Fixture\FixturesData\FixtureDataFormatter;


final readonly class FixturesDataBuilder
{
    public function __construct()
    {
        //
    }
    
    /**
     * build
     *
     * @param  Collection $fixturesData
     * @return Collection
     */
    public function build(Collection $fixturesData): Collection
    {
        $data = $fixturesData
            ->map(function ($fixtureData) {
                $fixtureData = new FixtureData($fixtureData);
                $formatter = new FixtureDataFormatter($fixtureData);
                
                return collect([
                    'external_fixture_id' => $fixtureData->getFixtureId(),
                    'external_league_id'  => $fixtureData->getLeagueId(),
                    'score'               => $formatter->formatScore()->toJson(),
                    'season'              => $fixtureData->getSeason(),
                    'date'                => $fixtureData->getDate(),
                    'status'              => $fixtureData->getStatus()
                ]);
            });

        /** @var Collection<int, Fixture> */
        $fixtures = Fixture::query()
            ->select(['id', 'external_fixture_id'])
            ->currentSeason()
            ->get();

        $result = $fixtures
            ? $data
                ->map(function ($fixture) use ($fixtures) {
                    $fixtureModel = $fixtures
                        ->keyBy('external_fixture_id')
                        ->get($fixture['external_fixture_id']);
                    
                    if (!$fixtureModel) {
                        return $fixture;
                    }
                    
                    return array_merge($fixture, $fixtureModel->toArray());
                })
                ->toArray()
            : $data->toArray();
            
        return collect([
            'original' => $fixturesData,
            'formatted' => $result
        ]);
    }
}