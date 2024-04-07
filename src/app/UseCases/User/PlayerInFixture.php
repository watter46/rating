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
    
    // 選手が出場しているか
    public function isPlayed()
    {

    }
    
    private function canRate(): bool
    {
        $specifiedDate = Carbon::parse($this->fixture->date);

        return $specifiedDate->diffInDays(now('UTC')) <= self::RATE_PERIOD_DAY;
    }

    public function request(PlayerInFixtureRequest $request)
    {
        $fixture = Fixture::query()
            ->currentSeason()
            ->inSeasonTournament()
            ->finished()
            ->findOrFail($request->getFixtureId());

        return $this->setAttribute(fixture: $fixture, request: $request);
    }

    public function addPlayerInfos(): self
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

    public function player(): self
    {
        $player = $this->fixture
            ->players()
            ->firstOrNew([
                'fixture_id' => $this->request->getFixtureId(),
                'player_info_id' => $this->request->getPlayerInfoId()
            ]);
                
        $player->canRate = $this->canRate();

        return $this->setAttribute(player: $player);
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