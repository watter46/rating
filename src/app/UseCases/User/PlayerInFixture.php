<?php declare(strict_types=1);

namespace App\UseCases\User;

use Exception;
use Illuminate\Support\Carbon;

use App\Models\Fixture;
use App\Models\Player;
use App\Models\PlayerInfo;


readonly class PlayerInFixture
{
    private const RATE_PERIOD_DAY = 50;
    public const RATE_PERIOD_EXPIRED_MESSAGE = 'Rate period has expired.';

    private const MAX_RATE_COUNT = 3;
    public const RATE_LIMIT_EXCEEDED_MESSAGE = 'Rate limit exceeded.';

    private const MAX_MOM_COUNT = 5;
    public const MOM_LIMIT_EXCEEDED_MESSAGE = 'MOM limit exceeded.';

    public function __construct(
        private Fixture $fixture,
        private Player $player,
        private ?PlayerInFixtureRequest $request = null)
    {
        //
    }
    
    /**
     * 評価可能な回数を超えているか判定する
     *
     * @return bool
     */
    public function exceedRateLimit(): bool
    {
        return $this->player->rate_count >= self::MAX_RATE_COUNT;
    }
    
    /**
     * MOMを選択できる回数を超えているか判定する
     *
     * @return bool
     */
    public function exceedMomLimit(): bool
    {
        return $this->fixture->mom_count >= self::MAX_MOM_COUNT;
    }
    
    /**
     * 評価可能期間を超えている判定する
     *
     * @return bool
     */
    public function exceedPeriodDay(): bool
    {
        $specifiedDate = Carbon::parse($this->fixture->date);
        
        return $specifiedDate->diffInDays(now('UTC')) > self::RATE_PERIOD_DAY;
    }
        
    /**
     * 評価可能か判定する
     *
     * @return bool
     */
    public function canRate(): bool
    {
        return !$this->exceedPeriodDay() && !$this->exceedRateLimit();
    }
    
    /**
     * MOMを選択可能か判定する
     *
     * @return bool
     */
    public function canMom(Player $player): bool
    {
        return !$this->exceedPeriodDay()
            && !$this->exceedMomLimit()
            && !$player->mom;
    }
    
    /**
     * Request
     *
     * @param  PlayerInFixtureRequest $request
     * @return self
     */
    public function request(PlayerInFixtureRequest $request): self
    {
        $fixture = Fixture::query()
            ->select(['id', 'date', 'fixture', 'mom_count'])
            ->currentSeason()
            ->inSeasonTournament()
            ->finished()
            ->findOrFail($request->getFixtureId());

        $player = $request->existsPlayerInfoId()
            ? Player::query()
                ->fixtureId($request->getFixtureId())
                ->playerInfoId($request->getPlayerInfoId())
                ->firstOrNew([
                    'fixture_id' => $request->getFixtureId(),
                    'player_info_id' => $request->getPlayerInfoId()
                ])
            : null;

        return $this->setAttribute(
                fixture: $fixture,
                player: $player,
                request: $request
            );
    }

    public function addPlayerColumn()
    {
        $playedIds = $this->fixture->toFixtureData()->getPlayerIds();
        
        $playerInfos = PlayerInfo::query()
            ->with(['players' => fn($query) => $query
                ->fixtureId($this->fixture->id)
            ])
            ->select(['id', 'foot_player_id'])
            ->currentSeason()
            ->whereIn('foot_player_id', $playedIds->toArray())
            ->get();

        if ($playerInfos->count() !== $playedIds->count()) {
            throw new Exception('PlayerInfo Not Found.');
        } 

        $this->fixture->playerInfos = $playerInfos;

        $players = $playerInfos->map(function (PlayerInfo $info) {
            $player = $info->players->first()
                ?? new Player([
                    'fixture_id' => $this->fixture->id,
                    'player_info_id' => $info->id
                ]);
            
            $player->canRate = $this->canRate();
            $player->rateLimit = self::MAX_RATE_COUNT;
            $player->canMom = $this->canMom($player);

            return $player;
        });

        $this->fixture->players = $players;
        
        return $this->setAttribute(fixture: $this->fixture);
    }
    
    /**
     * 最新の試合を取得する
     *
     * @return self
     */
    public function latest(): self
    {
        $fixture = Fixture::query()
            ->select(['id', 'date', 'fixture', 'mom_count'])
            ->currentSeason()
            ->inSeasonTournament()
            ->finished()
            ->untilToday()
            ->first();

        return $this->setAttribute(fixture: $fixture);
    }
    
    /**
     * CanRateカラムをFixtureに追加する
     *
     * @return self
     */
    public function addCanRateToFixture(): self
    {                
        $this->fixture->canRate = $this->canRate();
        $this->fixture->momLimit = self::MAX_MOM_COUNT;
        $this->fixture->exceedMomLimit = $this->exceedMomLimit();
        
        return $this->setAttribute(player: $this->player);
    }

    /**
     * CanRateカラムをPlayerに追加する
     *
     * @return self
     */
    public function addCanRateToPlayer(): self
    {
        $this->player->canRate = $this->canRate();
        $this->player->rateLimit = self::MAX_RATE_COUNT;
        $this->player->canMom = $this->canMom($this->player);

        return $this->setAttribute(player: $this->player);
    }

    public function getFixture(): Fixture
    {
        return $this->fixture;
    }

    public function getPlayer(): player
    {
        return $this->player;
    }
    
    /**
     * Momの現在のカウントと上限値を返す
     *
     * @return array{momLimit: int, mom_count: int, exceedMomCount: bool}
     */
    public function getMomCountAndLimit(): array
    {
        $this->fixture->momLimit = self::MAX_MOM_COUNT;
        $this->fixture->exceedMomLimit = $this->exceedMomLimit();

        return $this->fixture->only(['momLimit', 'mom_count', 'exceedMomLimit']);
    }

    private function setAttribute(
        ?Fixture $fixture = null,
        ?Player $player = null,
        ?PlayerInFixtureRequest $request = null): self
    {
        return new self(
                fixture: $fixture ?? $this->fixture,
                player:  $player  ?? $this->player,
                request: $request ?? $this->request
            );
    }
}