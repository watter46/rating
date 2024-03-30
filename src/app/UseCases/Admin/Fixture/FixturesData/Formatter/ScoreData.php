<?php declare(strict_types=1);

namespace App\UseCases\Admin\Fixture\FixturesData\Formatter;

use App\Http\Controllers\Util\LeagueImageFile;
use App\Http\Controllers\Util\TeamImageFile;


readonly class ScoreData
{
    public function __construct(
        private TeamImageFile $teamImage,
        private LeagueImageFile $leagueImage
    ) {}
    
    public function build($fixture): string
    {
        return collect($fixture)
            ->except(['goals', 'score'])
            ->map(function ($data, $key) {
                return match ($key) {
                    'fixture' => [
                            'date' => $data->date,
                            'status' => $data->status->long
                        ],
                    'teams' => collect($data)
                        ->map(function ($team) {
                            return [
                                'name' => $team->name,
                                'img'  => $this->teamImage->generatePath($team->id)
                            ];
                        })
                        ->toArray(),
                    'league' => [
                            'name' => $data->name,
                            'img'  => $this->leagueImage->generatePath($data->id),
                            'season' => $data->season,
                            'round' => $data->round
                        ]
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