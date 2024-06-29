<?php declare(strict_types=1);

namespace App\UseCases\Admin\Data\FlashLiveSports;

use App\Models\PlayerInfo;
use App\UseCases\Admin\Player\UpdatePlayerInfos\PlayerDataMatcher;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class TeamSquad
{
    private function __construct(private Collection $teamSquad)
    {
        //
    }
    
    public static function create(Collection $teamSquad): self
    {
        return (new self($teamSquad));
    }

    public function getByPlayerInfo(PlayerDataMatcher $matcher)
    {
        return $this->getPlayers()->first(fn ($player) => $matcher->match($player));
    }

    public function getPlayers(): Collection
    {
        return $this->teamSquad
            ->flatten(1)
            ->map(function ($group) {
                $swapFirstAndLastName = function ($name) {
                    $names = collect(Str::of($name)->explode(' '));
                                            
                    return $names->reverse()->implode(' ');
                };

                return collect($group->ITEMS)
                    ->map(fn($player) => [
                        'id' => $player->PLAYER_ID,
                        'name' => $swapFirstAndLastName($player->PLAYER_NAME),
                        'number' => $player->PLAYER_JERSEY_NUMBER
                    ]);
            })
            ->flatten(1);
    }

    public function imagePaths(): Collection
    {
        return $this->teamSquad
            ->flatten(1)
            ->map(function ($group) {
                return collect($group->ITEMS)
                    ->map(fn($player) => [
                        'id' => $player->PLAYER_ID,
                        'path' => $player->PLAYER_IMAGE_PATH
                    ]);
            })
            ->flatten(1);
    }
}