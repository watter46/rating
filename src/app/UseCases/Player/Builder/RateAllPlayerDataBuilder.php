<?php declare(strict_types=1);

namespace App\UseCases\Player\Builder;

use Illuminate\Support\Collection;
use App\Models\Player;


readonly class RateAllPlayerDataBuilder
{    
    /**
     * RateAllPlayerのデータを作成する
     *
     * @param  Collection<int, Player> $players
     * @param  Collection<int, array> $ratedPlayers
     * @param  string $fixtureId
     * @return array
     */
    public function build(Collection $players, Collection $ratedPlayers, string $fixtureId): array
    {                
        $updatedPlayers = $players
            ->map(function (Player $player) use ($ratedPlayers) {
                $target = $ratedPlayers
                    ->map(function (array $player) {
                        $player['player_info_id'] = $player['id'];
    
                        unset($player['id']);
    
                        return $player;
                    })
                    ->filter(function ($ratedPlayer) use ($player) {
                        return $ratedPlayer['player_info_id'] === $player->player_info_id;
                    })
                    ->first();

                $player->rating = $target['rating'];
                $player->mom = $target['mom'];
                $player->player_info_id = $target['player_info_id'];

                return $player;
            });

        $newRatingPlayers = $this->createNewPlayerModels($players, $ratedPlayers, $fixtureId);

        $data = $updatedPlayers
            ->when($updatedPlayers->isEmpty(), function (Collection $players) use ($newRatingPlayers) {
                return $newRatingPlayers;
            }, function (Collection $players) use ($newRatingPlayers) {
                return $players->push(...$newRatingPlayers);
            })
            ->toArray();
        
        return $data;
    }
    
    /**
     * 保存されていないプレイヤーのモデルを生成する
     *
     * @param  Collection<int, Player> $players
     * @param  Collection<int, Player> $ratedPlayers
     * @param  string $fixtureId
     * @return Collection<int, Player>
     */
    private function createNewPlayerModels(Collection $players, Collection $ratedPlayers, string $fixtureId): Collection
    {
        return $ratedPlayers
            ->whereNotIn('id', $players->pluck('player_info_id'))
            ->map(function (array $ratedPlayer) use ($fixtureId) {
                $player = (new Player)->associatePlayer($fixtureId, $ratedPlayer['id']);

                if ($ratedPlayer['mom']) {
                    return $player
                        ->decideMOM()
                        ->evaluate((float) $ratedPlayer['rating']);
                }
                
                return $player
                    ->unDecideMOM()
                    ->evaluate((float) $ratedPlayer['rating']);
            });
    }
}