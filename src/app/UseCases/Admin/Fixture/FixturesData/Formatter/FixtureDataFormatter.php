<?php declare(strict_types=1);

namespace App\UseCases\Admin\Fixture\FixturesData\Formatter;

use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

use App\Http\Controllers\Util\LeagueImageFile;
use App\Http\Controllers\Util\TeamImageFile;


readonly class FixtureDataFormatter
{
    public function __construct(private Collection $fixtureData)
    {
        //
    }

    public function getOriginalData(): Collection
    {
        return $this->fixtureData;
    }

    public function getFixtureId(): int
    {
        return $this->fixtureData->fixture->id;
    }

    public function getLeagueId(): int
    {
        return $this->fixtureData->league->id;
    }

    public function getScore(): string
    {
        /** @var TeamImageFile $teamImage */
        $teamImage = app(TeamImageFile::class);

        /** @var LeagueImageFile $leagueImage */
        $leagueImage = app(LeagueImageFile::class);

        return $this->fixtureData
            ->except(['goals', 'score'])
            ->map(function ($data, $key) use ($teamImage, $leagueImage) {
                return match ($key) {
                    'fixture' => [
                            'date' => $data->date,
                            'status' => $data->status->long
                        ],
                    'teams' => collect($data)
                        ->map(function ($team) use ($teamImage) {
                            return [
                                'name' => $team->name,
                                'img'  => $teamImage->generatePath($team->id)
                            ];
                        })
                        ->toArray(),
                    'league' => [
                            'name'   => $data->name,
                            'img'    => $leagueImage->generatePath($data->id),
                            'season' => $data->season,
                            'round'  => $data->round
                        ]
                };
            })
            ->toJson();
    }

    public function getSeason(): int
    {
        return $this->fixtureData->league->season;
    }

    public function getDate(): Carbon
    {
        return Carbon::parse($this->fixtureData->fixture->timestamp);
    }

    public function getStatus(): string
    {
        return $this->fixtureData->fixture->status->long;
    }
}