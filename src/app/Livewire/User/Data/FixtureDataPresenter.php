<?php declare(strict_types=1);

namespace App\Livewire\User\Data;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;

use App\Models\Fixture;
use App\Http\Controllers\Util\LeagueImageFile;
use App\Http\Controllers\Util\PlayerImageFile;
use App\Http\Controllers\Util\TeamImageFile;
use App\Livewire\User\Data\MobileSubstitutesSplitter;


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

        $this->formatTeamsImage();
        $this->formatLeagueImage();
    }
    
    public static function create(Fixture $fixture)
    {
        return new self($fixture);
    }

    public function get(): Fixture
    {
        return $this->fixture;
    }

    private function formatTeamsImage(): void
    {
        $this->fixture->fixtureInfo->teams = $this->fixture->fixtureInfo->teams
            ->map(function ($team) {

                $team['img'] = $this->teamImage->existsOrDefault($team['id']);

                return $team;  
            });
    }

    private function formatLeagueImage(): void
    {        
        $this->fixture->fixtureInfo->league = $this->fixture->fixtureInfo->league
            ->put('img', $this->leagueImage->existsOrDefault($this->fixture->fixtureInfo->league['id']));
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

        $mobile_substitutes = MobileSubstitutesSplitter::split($substitutesData)->get();

        $this->fixture->fixtureInfo->lineups['substitutes'] = $substitutesData;
        $this->fixture->fixtureInfo->lineups['mobile_substitutes'] = $mobile_substitutes;
        
        return new self($this->fixture);
    }
    
    /**
     * 選手のラストネームのみに変換する
     *
     * @param  string $dotValue
     * @return string
     */
    private function toLastName(string $dotValue): string
    {
        return Str::afterLast($dotValue, ' ');
    }
    
    /**
     * 選手のデータを表示用に変換する
     *
     * @param  Collection $playerInfos
     * @return self
     */
    public function formatPlayerData(Collection $playerInfos)
    {
        $players = $this->fixture->newPlayers->keyBy('player_info_id');

        $formatPlayer = function ($player) use ($playerInfos, $players) {
            $playerInfo = $playerInfos->keyBy('foot_player_id')->get($player['id']);

            $playerData = collect($player) 
                ->merge([
                    'id' => $playerInfo->id,
                    'name' => $this->toLastName($player['name']),
                    'rating' => $player['defaultRating'],
                    'img' => $this->playerImage->exists($player['id'])
                        ? $player['img']
                        : $this->playerImage->getDefaultPath(),
                ]);

            return [
                'playerData' => $playerData->toArray(),
                'player' => $players->get($playerData['id'])
            ];
        };

        $playerData = $this->fixture->fixtureInfo->lineups
            ->map(function ($lineups, $key) use ($formatPlayer) {
                if ($key === 'substitutes') {
                    return collect($lineups)->map(fn ($player) => $formatPlayer($player));
                }

                return collect($lineups)
                    ->map(fn($players) => collect($players)
                    ->map(fn ($player) => $formatPlayer($player)));
            });

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