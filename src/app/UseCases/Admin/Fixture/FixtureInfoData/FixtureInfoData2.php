<?php declare(strict_types=1);

namespace App\UseCases\Admin\Fixture\FixtureInfoData;

use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Str; 

use App\Http\Controllers\PositionType;
use App\Http\Controllers\Util\LeagueImageFile;
use App\Http\Controllers\Util\PlayerImageFile;
use App\Http\Controllers\Util\TeamImageFile;
use App\Models\FixtureStatusType;
use App\UseCases\Admin\Fixture\Data\FixtureData;
use App\UseCases\Admin\Fixture\Data\ResultData;
use App\UseCases\Admin\Fixture\DataInterface;
use Exception;

readonly class FixtureInfoData2 implements DataInterface
{
    private TeamImageFile $teamImage;
    private PlayerImageFile $playerImage;
    private LeagueImageFile $leagueImage;

    private const CHELSEA_TEAM_ID = 49;

    // FixtureInfoData -> fixture score league teams

    private function __construct(private Collection $fixtureData)
    {
        $this->teamImage   = new TeamImageFile();
        $this->playerImage = new PlayerImageFile();
        $this->leagueImage = new LeagueImageFile();
    }

    public static function create(Collection $fixtureData): self
    {
        FixtureData::create($fixtureData);
        
        return new self($fixtureData);
    }
    
    /**
     * モデルの更新用にデータを成形する
     *
     * @return Collection
     */
    public function build(): Collection
    {
        return collect([
            'fixture' => $this->getFixture(),
            'teams'   => $this->getTeams(),
            'league'  => $this->getLeague(),
            'score'   => $this->getScore(),
            'lineups' => $this->getLineups()
        ]);
    }
    
    /**
     * 試合を表示するのに必要なデータが存在しているか判定する
     *
     * @return bool
     */
    public function checkRequiredData(): bool
    {
        return FixtureInfoDataValidator::validate($this->fixtureData)->checkRequiredData();
    }
    
    public function validated(): FixtureInfoDataValidator
    {
        return FixtureInfoDataValidator::validate($this->fixtureData);
    }
    
    public function isFinished(): bool
    {
        return FixtureStatusType::from($this->getStatus())->isFinished();
    }

    public function getFixture(): Collection
    {
        return collect([
            'id'             => $this->getFixtureId(),
            'first_half_at'  => Carbon::parse($this->fixtureData->get('fixture')->periods->first, 'UTC'),
            'second_half_at' => Carbon::parse($this->fixtureData->get('fixture')->periods->second, 'UTC'),
            'is_end'         => $this->isFinished()
        ]);
    }

    public function getFixtureId(): int
    {
        return $this->fixtureData->get('fixture')->id;
    }

    public function getStatus(): string
    {
        return $this->fixtureData->get('fixture')->status->long;
    }

    public function getTeams(): Collection
    {
        $home = $this->fixtureData->get('teams')->home;
        $away = $this->fixtureData->get('teams')->away;
        
        return collect([
            'home' => collect([
                'id'     => $home->id,
                'name'   => $home->name,
                'img'    => $this->teamImage->generatePath($home->id),
                'winner' => $home->winner
            ]),
            'away' => collect([
                'id'     => $away->id,
                'name'   => $away->name,
                'img'    => $this->teamImage->generatePath($away->id),
                'winner' => $away->winner
            ])
        ]);
    }

    public function getLeague()
    {
        return collect([
            'id'     => $this->getLeagueId(),
            'name'   => $this->fixtureData->get('league')->name,
            'season' => $this->getSeason(),
            'round'  => $this->fixtureData->get('league')->round,
            'img'    => $this->leagueImage->generatePath($this->getLeagueId())
        ]);
    }

    public function getLeagueId(): int
    {
        return $this->fixtureData->get('league')->id;
    }

    public function getSeason(): int
    {
        return $this->fixtureData->get('league')->season;
    }

    public function getScore(): Collection
    {
        return collect($this->fixtureData->get('score'))->except('halftime');
    }

    public function filterChelsea(Collection $teams): Collection
    {
        $chelsea = $teams->sole(fn ($teams) => $teams->team->id === self::CHELSEA_TEAM_ID);
        
        return collect($chelsea);
    }

    public function getLineups(): Collection
    {
        $players = $this->playedPlayers()->keyBy('id');
        
        return $this->filterChelsea(collect($this->fixtureData->get('lineups')))
            ->only(['startXI', 'substitutes'])
            ->map(function ($lineups) use ($players) {
                return collect($lineups)
                    ->map(function ($lineup) use ($players) {
                        return collect($players->get($lineup->player->id))
                            ->merge([
                                'id'       => $lineup->player->id,
                                'name'     => $lineup->player->name,
                                'number'   => $lineup->player->number,
                                'position' => PositionType::from($lineup->player->pos)->name,
                                'grid'     => $lineup->player->grid,
                                'img'      => $this->playerImage->generatePath($lineup->player->id)
                            ]);
                    })
                    ->whereIn('id', $players->map(fn($player) => $player['id']));
            });
    }

    private function playedPlayers(): Collection
    {
        $chelsea = $this->filterChelsea(collect($this->fixtureData->get('players')));
        
        return collect($chelsea->get('players'))
            ->reject(function ($players) {
                return !$players->statistics[0]->games->minutes;
            })
            ->map(function ($players) {
                return [
                    'id' => $players->player->id,
                    'name' => $players->player->name,
                    'goal' => $players->statistics[0]->goals->total, 
                    'assists' => $players->statistics[0]->goals->assists, 
                    'defaultRating' => (float) $players->statistics[0]->games->rating,
                ];
            });
    }
}