<?php declare(strict_types=1);

namespace App\UseCases\User;

use Illuminate\Support\Collection;

use App\Models\Fixture;
use App\Models\FixtureInfo;
use App\Models\Player;
use App\Models\PlayerInfo;


class FixtureBuilder
{
    private FixtureValidator $validator;

    private const FIXTURE_INFO_SELECT_COLUMNS = 'id,score,teams,league,fixture,lineups'; 

    public function __construct(private Fixture $fixture, private ?Player $player = null)
    {
        $this->validator = new FixtureValidator($fixture);
    }

    public function validator()
    {
        return $this->validator;
    }

    public function loadAllInFixture(): self
    {
        return new self(
                $this->fixture
                    ->load([
                        'fixtureInfo:'.self::FIXTURE_INFO_SELECT_COLUMNS => ['playerInfos:id,foot_player_id'],
                        'players'
                    ]),
                $this->player
            );
    }

    public function loadAllIdInFixture(): self
    {
        return new self(
            $this->fixture
                ->load([
                    'fixtureInfo' => fn ($q) => $q
                        ->select('id')
                        ->withCount('playerInfos as playerCount'),
                    'ratedPlayers:id'
                ]),
            $this->player
        );
    }

    public function assignPlayer(FixtureRequest $request): self
    {
        $fixture = $this
            ->fixture
            ->load([
                'fixtureInfo:id,date',
                'players'
            ]);

        $playerInfoId = $request->getPlayerInfoId();

        $player = $fixture
            ->players
            ->first(function (Player $player) use ($playerInfoId) {
                return $player->player_info_id === $playerInfoId;
            })
            ?? new Player([
                'player_info_id' => $playerInfoId,
                'fixture_id' => $this->fixture->id
            ]);

        return new self($fixture, $player);
    }

    public function assignUpdatedPlayer(Player $player): self
    {
        return new self($this->fixture, $player);
    }

    public function addColumnValidationToPlayer(): self
    {
        return new self(
                $this->fixture,
                $this->player
                    ->setAttribute('canRate', $this->validator->canRate($this->player))
                    ->setAttribute('canMom', $this->validator->canMom($this->player))
                    ->setAttribute('rateLimit', $this->validator->getRateCountLimit())
            );
    }

    public function latest(): self
    {
        $fixtureInfo = FixtureInfo::query()
            ->select('id')
            ->currentSeason()
            ->inSeasonTournament()
            ->finished()
            ->untilToday()
            ->first();
            
        $fixture = Fixture::query()
            ->selectWithout()
            ->fixtureInfoId($fixtureInfo->id)
            ->firstOrNew(['fixture_info_id' => $fixtureInfo->id]);
            
        return new self($fixture, $this->player);
    }

    public function addMomLimit()
    {
        $this->fixture->momLimit = $this->validator->getMomCountLimit();
        
        return new self($this->fixture, $this->player);
    }

    public function addPlayers(): self
    {
        $newPlayers = $this->fixture->fixtureInfo->playerInfos
            ->map(function (PlayerInfo $playerInfo) {
                return collect(['player_info_id' => $playerInfo->id]);
            });

        $players = $this->fixture->players->isEmpty()
            ? $newPlayers
            : $this->fixture->players
                ->map(fn($p) => collect($p))
                ->merge($newPlayers);

        $this->fixture->newPlayers = $players
            ->unique('player_info_id')
            ->map(function (Collection $data) {
                $player = new Player($data->toArray());

                $player
                    ->setAttribute('canRate', $this->validator->canRate($player))
                    ->setAttribute('canMom', $this->validator->canMom($player))
                    ->setAttribute('rateLimit', $this->validator->getRateCountLimit());
                    
                return $player;
            });
            
        return new self($this->fixture, $this->player);
    }

    public function exceedRateLimit(): bool
    {
        return $this->validator->exceedRateLimit($this->player);
    }
    
    /**
     * MOMを選択できる回数を超えているか判定する
     *
     * @return bool
     */
    public function exceedMomLimit(): bool
    {
        return $this->validator->exceedMomLimit();
    }
    
    /**
     * 評価可能期間を超えている判定する
     *
     * @return bool
     */
    public function exceedPeriodDay(): bool
    {
        return $this->validator->exceedPeriodDay();
    }
    
    /**
     * getRatedCount
     *
     * @return array{ ratedCount: int, playerCount: int }
     */
    public function getRatedCount(): array
    {
        return [
            'ratedCount'  => $this->fixture->ratedPlayers->count(),
            'playerCount' => $this->fixture->fixtureInfo->playerCount
        ];
    }

    public function get(): Fixture
    {
        return $this->fixture;
    }

    public function getPlayer(): Player
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
        $this->fixture->momLimit = $this->validator->getMomCountLimit();
        $this->fixture->exceedMomLimit = $this->validator->exceedMomLimit();
        
        return $this->fixture->only(['momLimit', 'mom_count', 'exceedMomLimit']);
    }
}