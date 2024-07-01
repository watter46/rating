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

                $team['img'] = $this->teamImage->exists($team['id'])
                    ? $this->teamImage->generateViewPath($team['id'])
                    : $this->teamImage->defaultPath();

                return $team;  
            });
    }

    private function formatLeagueImage(): void
    {        
        $leagueId = $this->fixture->fixtureInfo->league['id'];
        
        $this->fixture->fixtureInfo->league = $this->fixture->fixtureInfo->league
            ->put('img', $this->leagueImage->exists($leagueId)
                ? $this->leagueImage->generateViewPath($leagueId)
                : $this->leagueImage->defaultPath()
            );
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
        $players = $this->fixture->players->keyBy('player_info_id');

        $formatPlayer = function (array $playerData) use ($playerInfos, $players) {
            /** @var PlayerInfo $playerInfo */
            $playerInfo = $playerInfos->keyBy('api_football_id')->get($playerData['id']);
            
            /** @var Player $player */
            $player = $players->get($playerInfo->id);

            return collect([
                'fixture_info_id' => $this->fixture->fixture_info_id,
                'player_info_id' => $player->player_info_id,
                'canRate' => $player->canRate,
                'canMom' => $player->canMom,
                'momCount' => $this->fixture->mom_count,
                'momLimit' => $this->fixture->momLimit,
                'rateCount' => $player->rate_count,
                'rateLimit' => $player->rateLimit,
                'img' => $this->playerImage->exists($playerData['id'])
                    ? $this->playerImage->generateViewPath($playerData['id'])
                    : $this->playerImage->getDefaultPath(),
                'goals' => $playerData['goal'],
                'grid' => $playerData['grid'],
                'name' => $this->toLastName($playerData['name']),
                'number' => $playerData['number'],
                'assists' => $playerData['assists'],
                'position' => $playerData['position'],
                'ratings' => [
                    'my' => [
                        'rating' => $player->rating,
                        'mom' => $player->mom
                    ],
                    'users' => [
                        'rating' => $player->average?->rating,
                        'mom' => $player->average?->mom
                    ],
                    'machine' => $playerData['rating']
                ]
            ]);
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