<?php declare(strict_types=1);

namespace App\UseCases\Player;

use Exception;
use Illuminate\Support\Collection;

use App\Models\Fixture;
use App\Models\Player;


final readonly class FetchPlayersUseCase
{
    public function __construct(private Player $player)
    {
        //
    }
    
    /**
     * 試合に出場したプレイヤーをすべて取得する
     *
     * @param  Collection $playerInfoIdList
     * @param  string $fixtureId
     * @return Collection<array{players: Collection<int, Player>, canRated: bool}>
     */
    public function execute(Collection $playerInfoIdList, string $fixtureId): Collection
    {
        try {
            /** @var Collection<int, Player> */
            $players = Player::query()
                ->select(['id', 'mom', 'rating', 'player_info_id'])
                ->fixture($fixtureId)
                ->whereIn('player_info_id', $playerInfoIdList)
                ->get();
                
            $data = $playerInfoIdList
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

            return collect([
                'players'  => $data,
                'canRated' => Fixture::find($fixtureId)->canRate()
            ]);

        } catch (Exception $e) {
            throw $e;
        }
    }
}