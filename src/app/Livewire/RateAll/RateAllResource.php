<?php declare(strict_types=1);

namespace App\Livewire\RateAll;

use Illuminate\Support\Collection;
use App\Models\Player;


readonly class RateAllResource
{    
    /**
     * Lineupsをプレイヤーの一覧に変換する
     *
     * @param  array $lineups
     * @return Collection
     */
    public function lineupsToPlayers(array $lineups): Collection
    {
        return collect($lineups)->flatten(2);
    }
    
    /**
     * PlayerDataをRatingの表示用とプロフィールの表示用に分ける
     *
     * @param  array $lineups
     * @param  Collection<int, Player> $players
     * @return Collection
     */
    public function format(array $lineups, Collection $players)
    {        
        $playerIds = $players
            ->map(function ($player) {
                return collect($player)
                    ->put('id', $player['player_info_id'])
                    ->forget('player_info_id')
                    ->toArray();
            })
            ->keyBy('id');
        
        $playersData = $this->lineupsToPlayers($lineups)
            ->map(function (array $player) use ($playerIds) {
                return collect($player)->merge($playerIds->get($player['id']));
            })
            ->mapWithKeys(function ($player, $key) {
                return [$key => [
                    'profile' => [
                        'id'       => $player['id'],
                        'img'      => $player['img'],
                        'name'     => $player['name'],
                        'number'   => $player['number'],
                        'position' => $player['position']
                    ],
                    'player' => [
                        'id'     => $player['id'],
                        'mom'    => $player['mom'],
                        'rating' => $player['rating']
                    ]
                ]];
            });

        return collect([
            'profiles' => $playersData->map(fn($player) => $player['profile']),
            'players'  => $playersData->map(fn($player) => $player['player'])
        ]);
    }
}
