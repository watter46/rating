<?php declare(strict_types=1);

namespace App\UseCases\Admin\Fixture\FixtureInfosData;

use App\Http\Controllers\Util\LeagueImageFile;
use App\Http\Controllers\Util\TeamImageFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;


readonly class FixtureInfosDetailData
{
    private TeamImageFile $teamImage;
    private LeagueImageFile $leagueImage;
    
    /**
     * Fixturesデータ内のFixture
     *
     * @param  Collection $data
     * @return void
     */
    private function __construct(private Collection $data)
    {
        $this->teamImage   = new TeamImageFile();
        $this->leagueImage = new LeagueImageFile();
    }

    public static function create(Collection $data)
    {
        return new self($data);
    }

    public function build(): Collection
    {
        return collect([
            'external_fixture_id' => $this->getFixtureId(),
            'external_league_id'  => $this->getLeagueId(),
            'season'              => $this->getSeason(),
            'date'                => $this->getDate(),
            'status'              => $this->getStatus(),
            'score'               => $this->getScore()->toJson(),
            'teams'               => $this->getTeams()->toJson(),
            'league'              => $this->getLeague()->toJson(),
            'fixture'             => $this->getScore()->toJson()
        ]);
    }

    public function getFixtureId(): int
    {
        return $this->data->get('fixture')->id;
    }

    public function getLeagueId(): int
    {
        return $this->data->get('league')->id;
    }

    public function getTeamIds(): Collection
    {
        return collect($this->data->get('teams'))
            ->map(fn($team) => $team->id)
            ->values();
    }

    public function getSeason(): int
    {
        return $this->data->get('league')->season;
    }

    public function getStatus(): string
    {
        return $this->data->get('fixture')->status->long;
    }

    public function getDate(): Carbon
    {
        return Carbon::parse($this->data->get('fixture')->date, 'UTC');
    }

    public function getFixture(): Collection
    {
        return collect([
            'id' => $this->getFixtureId(),
            // 'first_half_at' => 
            'date'   => $this->getDate(),
            'status' => $this->getStatus()
        ]);
    }

    public function getTeams(): Collection
    {
        $home = $this->data->get('teams')->home;
        $away = $this->data->get('teams')->away;
        
        return collect([
            'home' => [
                'name' => $home->name,
                'img'  => $this->teamImage->generatePath($home->id)
            ],
            'away' => [
                'name' => $away->name,
                'img'  => $this->teamImage->generatePath($away->id)
            ]
        ]);
    }

    public function getLeague(): Collection
    {
        $league = $this->data->get('league');

        return collect([
            'name'   => $league->name,
            'img'    => $this->leagueImage->generatePath($this->getLeagueId()),
            'season' => $this->getSeason(),
            'round'  => $league->round
        ]);
    }

    public function getScore(): Collection
    {
        return collect([
            'fixture' => $this->getFixture(),
            'teams'   => $this->getTeams(),
            'league'  => $this->getLeague()
        ]);
    }
}