<?php declare(strict_types=1);

namespace App\Livewire;

use Illuminate\Support\Collection;


readonly class RateAllPlayersResource
{
    public function lineupsToPlayers(array $lineups): Collection
    {
        return collect($lineups)->flatten(2);
    }

    public function format(array $lineups, Collection $playerModels)
    {        
        $players = $playerModels
            ->map(function ($player) {
                return collect($player)
                    ->put('id', $player['player_info_id'])
                    ->forget('player_info_id')
                    ->toArray();
            })
            ->keyBy('id');
        
        return $this->lineupsToPlayers($lineups)
            ->map(function (array $player) use ($players) {
                $merged = collect($player)->merge($players->get($player['id']));

                if (!$merged['rating']) {
                    return $merged->put('rating', $merged->get('defaultRating'));
                }

                return $merged;
            });
    }
}
