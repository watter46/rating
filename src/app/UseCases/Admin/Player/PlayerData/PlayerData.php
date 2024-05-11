<?php declare(strict_types=1);

namespace App\UseCases\Admin\Player\PlayerData;

use App\UseCases\Util\Season;
use Illuminate\Support\Collection;

class PlayerData
{
    public function __construct(private int $footPlayerId, private Collection $playerData)
    {
        
    }
    
    public static function create(int $footPlayerId, Collection $playerData): self
    {
        $validated = $playerData->count() === 1
            ? $playerData->first()
            : $playerData
                ->filter(function ($player) {
                    return $player->team->shortName === 'Chelsea'
                        || $player->team->nameCode === 'CFC';
                })
                ->first();
                
        return new self($footPlayerId, collect($validated)->fromStd());
    }

    public function getFootPlayerId(): int
    {
        return $this->footPlayerId;
    }

    public function getPlayerData(): Collection
    {
        return collect([
            'foot_player_id' => $this->footPlayerId,
            'sofa_player_id' => $this->playerData->isEmpty() ? null : $this->playerData['id'],
            'season' => Season::current(),
            'number' => $this->playerData['jerseyNumber'],
            'name' => $this->playerData['name']
        ]);
    }
}