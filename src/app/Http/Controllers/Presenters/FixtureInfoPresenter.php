<?php declare(strict_types=1);

namespace App\Http\Controllers\Presenters;

use Illuminate\Support\Collection;

use App\Http\Controllers\Util\LeagueImageFile;
use App\Http\Controllers\Util\PlayerImageFile;
use App\Http\Controllers\Util\TeamImageFile;


class FixtureInfoPresenter
{
    private TeamImageFile   $teamImage;
    private LeagueImageFile $leagueImage;

    public function __construct(private Collection $fixtureInfo)
    {        
        $this->teamImage   = new TeamImageFile;
        $this->leagueImage = new LeagueImageFile;
    }

    public function formatScore(): Collection
    {
        return $this->fixtureInfo->dataGet('score');
    }

    public function formatTeams(): Collection
    {
        return $this->fixtureInfo->dataGet('teams')
            ->map(function (Collection $team) {

                $team['img'] = $this->teamImage->exists($team['id'])
                    ? $this->teamImage->generateViewPath($team['id'])
                    : $this->teamImage->defaultPath();

                return $team;
            });
    }

    public function formatLeague(): Collection
    {
        $leagueId = $this->fixtureInfo->dataGet('league.id', false);
        
        return $this->fixtureInfo->dataGet('league')
            ->dataSet('img',
                $this->leagueImage->exists($leagueId)
                    ? $this->leagueImage->generateViewPath($leagueId)
                    : $this->leagueImage->defaultPath()
            );
    }

    public function formatFixture(): Collection
    {
        return $this->fixtureInfo->dataGet('fixture');
    }

    public function formatLineups(): Collection
    {
        $lineups = $this->fixtureInfo->dataGet('lineups');
        $playerInfos = $this->fixtureInfo->dataGet('player_infos');
        
        return (new LineupsPresenter($lineups,$playerInfos))->format();
    }
}