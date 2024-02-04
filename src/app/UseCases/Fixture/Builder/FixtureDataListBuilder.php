<?php declare(strict_types=1);

namespace App\UseCases\Fixture\Builder;

use Illuminate\Support\Collection;

use App\Models\Fixture;
use App\UseCases\ApiFootball\Fixtures\Score;


final readonly class FixtureDataListBuilder
{    
    const END_STATUS = 'Match Finished';

    public function __construct(private Score $score)
    {
        //
    }
    
    /**
     * build
     *
     * @param  array $fetched
     * @param  Collection<int, Fixture> $fixtures
     * @return array
     */
    public function build(array $fetched, Collection $fixtures): array
    {
        $data = collect($fetched)
            ->map(function ($fixture) {
                return [
                    'external_fixture_id' => $fixture->fixture->id,
                    'external_league_id'  => $fixture->league->id,
                    'score'               => $this->score->build($fixture),
                    'season'              => $fixture->league->season,
                    'date'                => date('Y-m-d H:i', $fixture->fixture->timestamp)
                ];
            });

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
            
        return $result;
    }
}