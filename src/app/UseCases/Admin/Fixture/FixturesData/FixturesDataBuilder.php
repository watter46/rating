<?php declare(strict_types=1);

namespace App\UseCases\Admin\Fixture\FixturesData;

use Illuminate\Support\Collection;

use App\Models\Fixture;
use App\UseCases\Admin\Fixture\FixturesData\Formatter\FixtureDataFormatter;


final readonly class FixturesDataBuilder
{
    public function __construct()
    {
        //
    }
    
    /**
     * build
     *
     * @param  FixtureDataFormatter $formatter
     * @return Collection
     */
    public function build(FixtureDataFormatter $formatter): Collection
    {
        $data = collect([
            'external_fixture_id' => $formatter->getFixtureId(),
            'external_league_id'  => $formatter->getLeagueId(),
            'score'               => $formatter->getScore(),
            'season'              => $formatter->getSeason(),
            'date'                => $formatter->getDate(),
            'status'              => $formatter->getStatus()
        ]);

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
            'original' => $formatter->getOriginalData(),
            'formatted' => $result
        ]);
    }
}