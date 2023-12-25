<?php declare(strict_types=1);

namespace App\UseCases\Player\Builder;


final readonly class FixtureDataListBuilder
{    
    const CHELSEA_TEAM_ID = 49;
    const END_STATUS = 'Match Finished';
    
    /**
     * build
     *
     * @param  mixed $fetched
     * @param  mixed $fixtureList
     * @return array
     */
    public function build($fetched, $fixtureList): array
    {
        $data = collect($fetched)
            ->map(function ($fixture) {      
                return [
                    'external_fixture_id' => $fixture->fixture->id,
                    'external_league_id'  => $fixture->league->id,
                    'season'              => $fixture->league->season,
                    'is_end'              => $fixture->fixture->status->long === self::END_STATUS,
                    'date'                => date('Y-m-d H:i', $fixture->fixture->periods->first),
                ];
            });

        $result = $fixtureList
            ? $data
                ->zip(collect($fixtureList))
                ->map(function ($fixture) {
                    return array_merge($fixture[0], $fixture[1]);
                })
                ->toArray()
            : $data->toArray();

        return $result;
    }
}