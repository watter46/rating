<?php declare(strict_types=1);

namespace App\Livewire\User\Data;

use Illuminate\Support\Collection;

use App\Models\Fixture;
use App\Http\Controllers\Util\LeagueImageFile;
use App\Http\Controllers\Util\TeamImageFile;
use App\Models\FixtureInfo;
use Illuminate\Support\Carbon;

class FixturesDataPresenter
{
    private TeamImageFile   $teamImage;
    private LeagueImageFile $leagueImage;

    private function __construct(private FixtureInfo $fixtureInfo)
    {
        $this->teamImage   = new TeamImageFile;
        $this->leagueImage = new LeagueImageFile;
    }
    
    public static function create(FixtureInfo $fixtureInfo)
    {
        return new self($fixtureInfo);
    }

    public function get(): FixtureInfo
    {
        return $this->fixtureInfo;
    }

    /**
     * リーグ画像をパスから取得する
     *
     * @return self
     */
    public function formatPathToLeagueImage(): self
    {
        $leagueData = $this->fixtureInfo->league;
        
        $leagueData->put('img', $this->leagueImage->getByPath($leagueData->get('img')));
        
        $this->fixtureInfo->league = $leagueData;

        return new self($this->fixtureInfo);
    }

    /**
     * チーム画像をパスから取得する
     *
     * @return self
     */
    public function formatPathToTeamImages(): self
    {
        $teams = $this->fixtureInfo->teams;
        
        $teamsData = $teams
            ->map(function ($team) {
                return collect($team)->put('img', $this->teamImage->getByPath($team['img']));
            });

        $this->fixtureInfo->teams = $teamsData;

        return new self($this->fixtureInfo);
    }
    
    /**
     * FixtureのデータをView用に変換する
     *
     * @return self
     */
    public function formatFixtureData(): self
    {
        $start_at = $this->fixtureInfo->fixture->get('first_half_at');
        
        $this->fixtureInfo->fixture['first_half_at'] = Carbon::parse($start_at)->__toString();
            
        return new self($this->fixtureInfo);
    }
}