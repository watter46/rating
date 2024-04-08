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
    public  const RATE_PERIOD_EXPIRED_MESSAGE = 'Rate period has expired.';

    public function __construct(
        private Fixture $fixture,
        private Player $player,
        private ?PlayerInFixtureRequest $request = null)
    {
        //
    }
        
    /**
     * 評価可能期間を超えていないか判定する
     *
     * @return bool
     */
    public function canRate(): bool
    {
        $specifiedDate = Carbon::parse($this->fixture->date);

        return $specifiedDate->diffInDays(now('UTC')) <= self::RATE_PERIOD_DAY;
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
    
    /**
     * FixtureのカラムにFixtureDataから出場した選手のPlayerInfoを設定する
     *
     * @return self
     */
    public function addPlayerInfosColumn(): self
    {   
        $playedIds = $this->fixture->toFixtureData()->getPlayerIds();
        
        $playerInfos = PlayerInfo::query()
            ->currentSeason()
            ->whereIn('foot_player_id', $playedIds->toArray())
            ->get();

        if ($playerInfos->count() !== $playedIds->count()) {
            throw new Exception('PlayerInfo Not Found.');
        }

        $this->fixture->playerInfos = $playerInfos;

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
            ->currentSeason()
            ->inSeasonTournament()
            ->finished()
            ->untilToday()
            ->first();

        return $this->setAttribute(fixture: $fixture);
    }
    
    /**
     * CanRateカラムをPlayerに追加する
     *
     * @return self
     */
    public function addCanRateColumn(): self
    {                
        $this->player->canRate = $this->canRate();

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