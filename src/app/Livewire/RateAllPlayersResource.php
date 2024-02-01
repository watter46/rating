<?php declare(strict_types=1);

namespace App\Livewire;

use App\UseCases\Player\FetchPlayersUseCase;
use App\UseCases\Player\RateAllPlayersUseCase;
use Exception;
use Illuminate\Support\Collection;
use Livewire\Component;
use Illuminate\Support\Str;


readonly class RateAllPlayersResource
{
    public function __construct()
    {
        
    }

    public function lineupsToPlayers(array $lineups): Collection
    {
        return collect($lineups)
            ->map(function ($lineups, $key) {
                if ($key === 'startXI') {
                    return collect($lineups)->flatten(1);
                }

                return $lineups; 
            })
            ->flatten(1);
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
