<?php declare(strict_types=1);

namespace App\UseCases\Player;

use App\UseCases\Util\Season;
use Illuminate\Support\Collection;


readonly class ValidatePlayerData
{    
    private function __construct(private object $playerData)
    {
        //
    }

    public static function validate(Collection $playerData): self
    {
        $validated = $playerData->count() === 1
            ? $playerData->first()
            : $playerData
                ->filter(function ($player) {
                    return $player->team->shortName === 'Chelsea'
                        || $player->team->nameCode === 'CFC';
                })
                ->first();

        return new self($validated);
    }

    public function createData(array $player)
    {
        if (empty($this->playerData)) {
            return [
                'foot_player_id' => $player['id'],
                'sofa_player_id' => null,
                'season' => Season::current(),
                'number' => $player['number'],
                'name' => $player['name']
            ];
        }

        return [
            'foot_player_id' => $player['id'],
            'sofa_player_id' => $this->playerData->id,
            'season' => Season::current(),
            'number' => $player['number'],
            'name' => $player['name']
        ];
    }
}