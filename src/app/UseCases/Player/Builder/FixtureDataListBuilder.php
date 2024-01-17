<?php declare(strict_types=1);

namespace App\UseCases\Player\Builder;

use App\Http\Controllers\Util\LeagueImageFile;
use App\Http\Controllers\Util\TeamImageFile;

final readonly class FixtureDataListBuilder
{    
    const CHELSEA_TEAM_ID = 49;
    const END_STATUS = 'Match Finished';

    public function __construct(
        private TeamImageFile $teamImage,
        private LeagueImageFile $leagueImage)
    {
        
    }
    
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
                    'score'               => $this->score($fixture),
                    'season'              => $fixture->league->season,
                    'is_end'              => $fixture->fixture->status->long === self::END_STATUS,
                    'date'                => date('Y-m-d H:i', $fixture->fixture->timestamp),
                ];
            });

        $result = $fixtureList
            ? $data
                ->map(function ($fixture) use ($fixtureList) {
                    $filtered = collect($fixtureList)
                        ->first(function ($model) use ($fixture) {
                            return $model['external_fixture_id'] === $fixture['external_fixture_id'];
                        });

                    if (!$filtered) {
                        return $fixture;
                    }
                    
                    return array_merge($fixture, $filtered);
                })
                ->toArray()
            : $data->toArray();
            
        return $result;
    }

    private function score($fixture)
    {
        return collect($fixture)
            ->except(['goals', 'score'])
            ->map(function ($data, $key) {
                return match ($key) {
                    'fixture' => $this->fixture($data),
                    'teams'   => $this->teams($data),
                    'league'  => $this->league($data)
                };
            })
            ->toJson();
    }

    private function fixture($data)
    {
        return [
            'date' => $data->date,
            'status' => $data->status->long
        ];
    }

    private function teams($data)
    {
        return collect($data)
            ->map(function ($team) {
                return [
                    'name' => $team->name,
                    'img'  => $this->teamImage->generatePath($team->id)
                ];
            })
            ->toArray();
    }

    private function league($data)
    {
        return [
            'name' => $data->name,
            'img'  => $this->leagueImage->generatePath($data->id),
            'season' => $data->season,
            'round' => $data->round
        ];
    }
}