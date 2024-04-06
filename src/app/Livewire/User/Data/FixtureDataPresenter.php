<?php declare(strict_types=1);

namespace App\Livewire\User\Data;

use App\Http\Controllers\Util\LeagueImageFile;
use App\Http\Controllers\Util\PlayerImageFile;
use App\Http\Controllers\Util\TeamImageFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

use App\Models\Fixture;


readonly class FixtureDataPresenter
{
    private TeamImageFile   $teamImage;
    private LeagueImageFile $leagueImage;
    private PlayerImageFile $playerImage;

    private function __construct(private Collection $fixtureData)
    {
        $this->teamImage   = new TeamImageFile;
        $this->leagueImage = new LeagueImageFile;
        $this->playerImage = new PlayerImageFile;
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
     * 出場した選手数をカウントする
     *
     * @return self
     */
    public function playerCount(): self
    {
        $count = $this->fixtureData
            ->dataGet('lineups')
            ->flatten(1)
            ->count();

        $formatted = $this->fixtureData->dataSet('playerCount', $count);

        return new self($formatted);
    }
    
    /**
     * 先発出場した選手を表示用に成形する
     *
     * @return self
     */
    public function formatFormation(): self
    {        
        $startXI = collect($this->fixtureData->dataGet('lineups.startXI'))
            ->reverse()
            ->groupBy(function ($player) {
                return Str::before($player['grid'], ':');
            })
            ->values();

        $formatted = $this->fixtureData->dataSet('lineups.startXI', $startXI);

        return new self($formatted);
    }
    
    /**
     * 控えの選手を表示用に成形する
     *
     * @return self
     */
    public function formatSubstitutes(): self
    {
        $substitutesData = collect($this->fixtureData->dataGet('lineups.substitutes'));

        $substitutes = SubstitutesSplitter::split($substitutesData)->get();

        $formatted = $this->fixtureData->dataSet('lineups.substitutes', $substitutes);

        return new self($formatted);
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

    private function toLastName(string $dotValue): string
    {
        return Str::afterLast($dotValue, ' ');
    }

    public function formatPlayerData(Collection $playerInfos)
    {
        $playerData = $this->fixtureData->dataGet('lineups')
            ->map(fn($lineups) => collect($lineups)
                ->map(fn($players) => collect($players)
                    ->map(function ($player) use ($playerInfos) {
                        $playerInfo = $playerInfos->keyBy('foot_player_id')->get($player['id']);

                        $player['id']     = $playerInfo->id;
                        $player['name']   = $this->toLastName($player['name']);
                        $player['img']    = $this->playerImage->getByPath($player['img']);
                        $player['rating'] = $playerInfo->rating;

                        return $player;
                    })
            ));
        
        $formatted = $this->fixtureData->dataSet('lineups', $playerData);

        return new self($formatted);
    }
}