<?php declare(strict_types=1);

namespace App\UseCases\Admin\Fixture;

use Illuminate\Support\Collection;

use App\Models\Fixture;
use App\UseCases\Fixture\Format\FixtureList\Score;


final readonly class FixturesDataBuilder
{    
    const END_STATUS = 'Match Finished';

    public function __construct(private Score $score)
    {
        //
    }
    
    /**
     * build
     *
     * @param  Collection $fixturesData
     * @param  Collection<int, Fixture> $fixtures
     * @return array
     */
    public function build(Collection $fixturesData, Collection $fixtures): array
    {
        $data = collect($fixturesData)
            ->map(function ($fixture) {
                return [
                    'external_fixture_id' => $fixture->fixture->id,
                    'external_league_id'  => $fixture->league->id,
                    'score'               => $this->score->build($fixture),
                    'season'              => $fixture->league->season,
                    'date'                => date('Y-m-d H:i', $fixture->fixture->timestamp),
                    'status'              => $fixture->fixture->status->long
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