<?php declare(strict_types=1);

namespace App\UseCases\Fixture\Format\FixtureList;

use App\Http\Controllers\Util\LeagueImageFile;
use App\Http\Controllers\Util\TeamImageFile;


readonly class Score
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