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

    private function __construct(private Fixture $fixture)
    {
        $this->teamImage   = new TeamImageFile;
        $this->leagueImage = new LeagueImageFile;
        $this->playerImage = new PlayerImageFile;
    }
    
    public static function create(Fixture $fixture)
    {
        return new self($fixture);
    }

    public function get(): Fixture
    {
        return $this->fixture;
    }
    
    /**
     * 先発出場した選手を表示用に成形する
     *
     * @return self
     */
    public function formatFormation(): self
    {
        $startXI = $this->fixture->fixtureInfo->lineups->dataGet('startXI')
            ->reverse()
            ->groupBy(function ($player) {
                return Str::before($player['grid'], ':');
            })
            ->values()
            ->toArray();
        
        $this->fixture->fixtureInfo->lineups['startXI'] = $startXI;

        return new self($this->fixture);
    }
    
    /**
     * 控えの選手を表示用に成形する
     *
     * @return self
     */
    public function formatSubstitutes(): self
    {
        $substitutesData = $this->fixture->fixtureInfo->lineups->dataGet('substitutes');

        $substitutes = SubstitutesSplitter::split($substitutesData)->get();

        $this->fixture->fixtureInfo->lineups['substitutes'] = $substitutes;

        return new self($this->fixture);
    }
    
    /**
     * リーグ画像をパスから取得する
     *
     * @return self
     */
    public function formatPathToLeagueImage(): self
    {
        $leagueData = $this->fixture->fixtureInfo->league;

        $leagueData->put('img', $this->leagueImage->getByPath($leagueData->get('img')));

        $this->fixture->fixtureInfo->league = $leagueData;

        return new self($this->fixture);
    }

    /**
     * チーム画像をパスから取得する
     *
     * @return self
     */
    public function formatPathToTeamImages(): self
    {
        $teams = $this->fixture->fixtureInfo->teams;
        
        $teamsData = $teams
            ->map(function ($team) {
                return collect($team)->put('img', $this->teamImage->getByPath($team['img']));
            });

            $this->fixture->fixtureInfo->teams = $teamsData;

        return new self($this->fixture);
    }

    private function toLastName(string $dotValue): string
    {
        return Str::afterLast($dotValue, ' ');
    }

    public function formatPlayerData(Collection $playerInfos)
    {
        $playerData = $this->fixture->fixtureInfo->lineups
            ->map(fn($lineups) => collect($lineups)
                ->map(fn($players) => collect($players)
                    ->map(function ($player) use ($playerInfos) {
                        $playerInfo = $playerInfos->keyBy('foot_player_id')->get($player['id']);

                        $player['id']     = $playerInfo->id;
                        $player['name']   = $this->toLastName($player['name']);
                        $player['img']    = $this->playerImage->getByPath($player['img']);
                        $player['rating'] = $player['defaultRating'];

                        return $player;
                    })
            ));

        $this->fixture->fixtureInfo->lineups = $playerData;

        return new self($this->fixture);
    }

    /**
     * 先発する選手の数をカラムに追加する
     *
     * @return self
     */
    public function addPlayerCountColumn()
    {
        $count = $this->fixture->fixtureInfo->lineups->dataGet('startXI')
            ->flatten(1)
            ->count();

        $this->fixture->fixtureInfo->lineups['playerCount'] = $count;

        return new self($this->fixture);
    }
}