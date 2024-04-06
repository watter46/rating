<?php declare(strict_types=1);

namespace App\Livewire\User\Data;

use Illuminate\Support\Collection;

use App\Models\Fixture;
use App\Http\Controllers\Util\LeagueImageFile;
use App\Http\Controllers\Util\TeamImageFile;
use Illuminate\Support\Carbon;

class FixturesDataPresenter
{
    private TeamImageFile   $teamImage;
    private LeagueImageFile $leagueImage;

    private function __construct(private Collection $fixtureData)
    {
        $this->teamImage   = new TeamImageFile;
        $this->leagueImage = new LeagueImageFile;
    }
    
    public static function create(Fixture $fixture)
    {
        return new self($fixture->fixture);
    }

    public function get(): Collection
    {
        return $this->fixtureData;
    }

    /**
     * リーグ画像をパスから取得する
     *
     * @return self
     */
    public function formatPathToLeagueImage(): self
    {
        $leagueData = $this->fixtureData->dataGet('league');
        
        $leagueData->put('img', $this->leagueImage->getByPath($leagueData->get('img')));

        $formatted = $this->fixtureData->dataSet('league', $leagueData);

        return new self($formatted);
    }

    /**
     * チーム画像をパスから取得する
     *
     * @return self
     */
    public function formatPathToTeamImages(): self
    {
        $teams = $this->fixtureData->dataGet('teams');
        
        $teamsData = $teams
            ->map(function ($team) {
                return collect($team)->put('img', $this->teamImage->getByPath($team['img']));
            });

        $formatted = $this->fixtureData->dataSet('teams', $teamsData);

        return new self($formatted);
    }
    
    /**
     * FixtureのデータをView用に変換する
     *
     * @return self
     */
    public function formatFixtureData(): self
    {
        $winner = $this->fixtureData->dataGet('teams')
            ->sole(function ($team) {
                return $team['id'] === config('api-football.chelsea-id');
            })['winner'];

        $start_at = $this->fixtureData->dataGet('fixture')->get('first_half_at');
        
        $formatted = $this->fixtureData
            ->dataSet('fixture.winner', $winner)
            ->dataSet('fixture.first_half_at', Carbon::parse($start_at)->__toString());

        return new self($formatted);
    }
}