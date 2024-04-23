<?php declare(strict_types=1);

namespace App\UseCases\Admin\Fixture\Data;

use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

use App\Http\Controllers\Util\LeagueImageFile;
use App\Http\Controllers\Util\TeamImageFile;


class ResultData
{
    private TeamImageFile $teamImage;
    private LeagueImageFile $leagueImage;
    
    /**
     * - score
     * - teams
     * - league
     * - fixture
     *
     * @param  Collection $resultData
     * @return void
     */
    public function __construct(private Collection $resultData)
    {
        $this->teamImage   = new TeamImageFile();
        $this->leagueImage = new LeagueImageFile();
    }

    public static function create(Collection $resultData): self
    {
        return new self($resultData);
    }

    public function build(): Collection
    {
        return collect([
                'fixture' => $this->getFixture(),
                'teams'   => $this->getTeams(),
                'league'  => $this->getLeague(),
                'score'   => $this->getScore()
            ]);
    }
    
    public function isFinished(): bool
    {
        $status = FixtureStatusType::tryFrom($this->getStatus()) ?? FixtureStatusType::OtherStatus;
        
        return $status->isFinished();
    }

    public function getFixtureId(): int
    {
        return $this->resultData->dataGet('fixture.id', false);
    }

    public function getStatus(): string
    {
        return $this->resultData->dataGet('fixture.status.long', false);
    }

    public function getLeagueId(): int
    {
        return $this->resultData->dataGet('league.id', false);
    }

    public function getSeason(): int
    {
        return $this->resultData->dataGet('league.season', false);
    }

    public function getDate(): Carbon
    {
        return Carbon::parse($this->resultData->dataGet('fixture.periods.first', false), 'UTC');
    }

    public function getFixture(): Collection
    {
        return collect([
            'id'             => $this->getFixtureId(),
            'first_half_at'  => $this->getDate(),
            'second_half_at' => Carbon::parse($this->resultData->dataGet('fixture.periods.second', false), 'UTC'),
            'is_end'         => $this->isFinished()
        ]);
    }

    public function getTeamIds(): Collection
    {
        $home = $this->resultData->dataGet('teams.home.id');
        $away = $this->resultData->dataGet('teams.away.id');

        return $home->merge($away);
    }

    public function getTeams(): Collection
    {
        $home = $this->resultData->dataGet('teams.home');
        $away = $this->resultData->dataGet('teams.away');

        return collect([
            'home' => collect([
                'id'     => $home['id'],
                'name'   => $home['name'],
                'img'    => $this->teamImage->generatePath($home['id']),
                'winner' => $home['winner']
            ]),
            'away' => collect([
                'id'     => $away['id'],
                'name'   => $away['name'],
                'img'    => $this->teamImage->generatePath($away['id']),
                'winner' => $away['winner']
            ])
        ]);
    }

    public function getLeague(): Collection
    {
        return collect([
            'id'     => $this->getLeagueId(),
            'name'   => $this->resultData->dataGet('league.name', false),
            'season' => $this->getSeason(),
            'round'  => $this->resultData->dataGet('league.round', false),
            'img'    => $this->leagueImage->generatePath($this->getLeagueId())
        ]);
    }

    public function getScore(): Collection
    {
        return $this->resultData->dataGet('score')->except('halftime');
    }
}