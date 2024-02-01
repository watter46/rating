<?php declare(strict_types=1);

namespace App\UseCases\Player;

use App\Models\Player;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Collection;

final readonly class FetchPlayersUseCase
{
    public function __construct(private Player $player)
    {
        //
    }

    public function execute(Collection $playerInfoIdList, string $fixtureId): Collection
    {
        try {
            /** @var Collection<int, Player> */
            $players = Player::query()
                ->select(['id', 'mom', 'rating', 'player_info_id'])
                ->fixture($fixtureId)
                ->whereIn('player_info_id', $playerInfoIdList)
                ->get();
                
            $result = $playerInfoIdList
                ->map(fn ($playerInfoId) =>(new Player)->init($playerInfoId))
                ->when($players->isNotEmpty(), function (Collection $newPlayers) use ($players) {
                    return $newPlayers
                        ->map(function (Player $newPlayer) use ($players) {
                            $player = $players
                                ->keyBy('player_info_id')
                                ->get($newPlayer->player_info_id);

                            if (!$player) {
                                return $newPlayer;
                            }
                            
                            return $player;
                        });

                }, function (Collection $newPlayers) {
                    return $newPlayers->map(fn(Player $player) => $player->toArray());
                });

            return $result;

        } catch (ModelNotFoundException $e) {
            throw new ModelNotFoundException('プロジェクトが見つかりませんでした。');

        } catch (Exception $e) {
            throw $e;
        }
    }
}