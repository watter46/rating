<?php declare(strict_types=1);

namespace App\UseCases\Admin\Fixture\FixturesData;

use Illuminate\Support\Collection;

use App\Http\Controllers\Util\LeagueImageFile;
use App\Http\Controllers\Util\TeamImageFile;


class FixtureDataFormatter
{
    private TeamImageFile $teamImage;
    private LeagueImageFile $leagueImage;

    public function __construct(private FixtureData $fixtureData)
    {
        $this->teamImage   = new TeamImageFile();
        $this->leagueImage = new LeagueImageFile();
    }

    public function formatScore(): Collection
    {
        return collect([
            'fixture' => [
                'date'   => $this->fixtureData->getDate(),
                'status' => $this->fixtureData->getStatus()
            ],
            'teams' => [
                'home' => [
                    'name' => $this->fixtureData->getHomeTeam()->get('name'),
                    'img'  => $this->teamImage->generatePath($this->fixtureData->getHomeTeam()->get('id'))
                ],
                'away' => [
                    'name' => $this->fixtureData->getAwayTeam()->get('name'),
                    'img'  => $this->teamImage->generatePath($this->fixtureData->getAwayTeam()->get('id'))
                ]
            ],
            'league' => [
                'name'   => $this->fixtureData->getLeagueName(),
                'img'    => $this->leagueImage->generatePath($this->fixtureData->getLeagueId()),
                'season' => $this->fixtureData->getSeason(),
                'round'  => $this->fixtureData->getRound()
            ]
        ]);
    }
}