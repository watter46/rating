<?php declare(strict_types=1);

namespace App\UseCases\Admin\Fixture\Accessors;

use Illuminate\Support\Collection;

use App\UseCases\Admin\Fixture\Accessors\PositionType;


class Lineups
{
    private const LINEUPS_DATA_KEYS = ['lineups', 'statistics', 'players'];
        
    /**
     * __construct
     *
     * @param  Collection<LineupPlayer> $lineups
     * @return void
     */
    private function __construct(private Collection $lineups)
    {

    }

    public static function create(Collection $data): self
    {
        $chelsea = $data
            ->only(self::LINEUPS_DATA_KEYS)
            ->mapWithKeys(function (Collection $teams, string $key) {
                return [
                    $key => $teams
                        ->first(function (Collection $team) {
                            return $team->dataGet('team.id', false) === config('api-football.chelsea-id');
                        })
                ];
            });

        $playersKeyById = $chelsea
            ->dataGet('players.players')
            ->map(function (Collection $player) {
                return [
                    'id'      => $player->dataGet('player.id', false),
                    'name'    => PlayerName::create($player->dataGet('player.name', false))->getFullName(),
                    'number'  => $player->dataGet('statistics.0.games.number', false),
                    'goal'    => $player->dataGet('statistics.0.goals.total', false), 
                    'assists' => $player->dataGet('statistics.0.goals.assists', false), 
                    'rating'  => $player->dataGet('statistics.0.games.rating', false),
                    'minutes' => $player->dataGet('statistics.0.games.minutes', false)
                ];
            })
            ->filter(fn(array $player) => !is_null($player['minutes']))
            ->keyBy('id');
        
        $playedIds = $playersKeyById->pluck('id');
            
        $lineups = $chelsea
            ->dataGet('lineups')
            ->only(['startXI', 'substitutes'])
            ->map(function (Collection $lineups) use ($playedIds) {
                return $lineups->flatten(1)->whereIn('id', $playedIds);
            })
            ->map(function (Collection $lineups) use ($playersKeyById) {
                return $lineups
                    ->map(function (Collection $player) use ($playersKeyById) {
                        $data = $player->merge($playersKeyById->get($player['id']));

                        return collect([
                                'id'       => $data['id'],
                                'name'     => $data['name'],
                                'number'   => $data['number'],
                                'position' => PositionType::from($data['pos']),
                                'grid'     => $data['grid'],
                                'goal'     => $data['goal'], 
                                'assists'  => $data['assists'], 
                                'rating'   => $data['rating'],
                                'minutes'  => $data['minutes']
                            ]);
                    })
                    ->map(fn(Collection $player) => $player->except('minutes'));
            });
            
        return new self(
            $lineups
                ->map(function (Collection $lineup) {
                    return $lineup
                        ->map(function (Collection $player) {
                            return LineupPlayer::create($player);
                        });
                })
        );
    }

    public static function reconstruct(Collection $lineups, Collection $playerInfos): self
    {
        $playerInfoModelsByApiId = $playerInfos->keyBy('api_player_id');
        
        return (new self(
            $lineups
                ->toCollection()
                ->map(function (Collection $lineup) use ($playerInfoModelsByApiId) {
                    return $lineup
                        ->map(function (Collection $player) use ($playerInfoModelsByApiId) {
                            $playerInfoModel = $playerInfoModelsByApiId->get($player['id']);
                            
                            return LineupPlayer::reconstruct($player, $playerInfoModel);
                        });
                })
        ));
    }

    public function updatePlayerInfos(Collection $playerInfoModels)
    {
        $keyByPlayerId = $playerInfoModels->keyBy('api_player_id');
        
        $lineups = $this->lineups
            ->map(function (Collection $players) use ($keyByPlayerId) {
                return $players
                    ->map(function (LineupPlayer $player) use ($keyByPlayerId) {
                        $playerInfoModel = $keyByPlayerId->get($player->getPlayerId());

                        if ($playerInfoModel) {
                            return $player->assignPlayerInfo($playerInfoModel);
                        }
                        
                        return $player;
                    });
            });

        return new self($lineups);
    }
    
    public function equalLineupsPlayerInfosCount(): bool
    {
        $lineupsCount = $this->getPlayers()->count();

        $playerInfoCount = $this->lineups
            ->flatten(1)
            ->filter(fn (LineupPlayer $player) => $player->existPlayerInfo())
            ->count();

        return $lineupsCount === $playerInfoCount;
    }

    public function hasImages(): bool
    {
        return $this->getPlayers()
            ->every(fn(LineupPlayer $player) => $player->hasImage());
    }

    public function areAllPlayersValid()
    {
        return $this->getPlayers()
            ->every(fn (LineupPlayer $player) => $player->isValid());
    }

    public function toModel(): Collection
    {
        return $this->lineups
            ->map(function (Collection $players) {
                return $players->map(fn(LineupPlayer $player) => $player->toModel());
            });
    }

    public function getPlayerIds(): Collection
    {
        return $this->getPlayers()
            ->map(fn(LineupPlayer $player) => $player->getPlayerId());
    }

    private function getPlayers(): Collection
    {
        return $this->lineups->flatten(1);
    }

    public function getInvalidPlayers(): Collection
    {
        return $this->getPlayers()
            ->filter(fn (LineupPlayer $player) => !$player->isValid());
    }

    public function getInvalidImagePlayers()
    {
        return $this->getPlayers()
            ->filter(fn (LineupPlayer $player) => !$player->hasImage());
    }

    public function getNeedsRegisterPlayerIds()
    {
        return $this->getPlayers()
            ->filter(fn (LineupPlayer $player) => $player->needsRegister())
            ->map(fn (LineupPlayer $player) => $player->getPlayerId());
    }
}