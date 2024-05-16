<?php declare(strict_types=1);

namespace App\Livewire\User\Data;

use Illuminate\Support\Carbon;

use App\Http\Controllers\Util\LeagueImageFile;
use App\Http\Controllers\Util\TeamImageFile;
use App\Models\FixtureInfo;


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
     * FixtureのデータをView用に変換する
     *
     * @return self
     */
    public function formatFixtureData(): self
    {
        $start_at = $this->fixtureInfo->fixture->get('first_half_at');
        
        $this->fixtureInfo->fixture['first_half_at'] = Carbon::parse($start_at)->format('Y/m/d');
            
        return new self($this->fixtureInfo);
    }
}