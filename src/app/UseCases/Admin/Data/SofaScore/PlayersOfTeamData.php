<?php declare(strict_types=1);

namespace App\UseCases\Admin\Data\SofaScore;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;

use App\Models\PlayerInfo;
use App\UseCases\Admin\Player\UpdatePlayerInfos\PlayerDataMatcher;


readonly class PlayersOfTeamData
{
    private function __construct(private Collection $playersOfTeamData)
    {
        
    }
    
    public static function create(Collection $playersOfTeamData): self
    {
        return new self($playersOfTeamData);
    }

    private function getPlayers(): Collection
    {
        return collect($this->playersOfTeamData->get('players'))
            ->map(function ($players) {
                return [
                    'id' => $players->player->id,
                    'name' => $players->player->name,
                    'number' => $players->player->shirtNumber
                ];
            });
    }
    
    // public function getBySquadsData(Collection $squadsData): Collection
    // {        
    //     $player = $this->getPlayersData()
    //         ->filter(function ($player) use ($squadsData) {
    //             return $player->get('name') === $squadsData->get('name')
    //                 && $player->get('number') === $squadsData->get('number');
    //         });

    //     if ($player->isNotEmpty()) {
    //         return $player;
    //     }

    //     $player = $this->getPlayersData()
    //         ->filter(function ($player) use ($squadsData) {                                
    //             return Str::afterLast($player->get('name'), ' ') === Str::afterLast($squadsData->get('name'), ' ')
    //                 && $player->get('number') === $squadsData->get('number');
    //         });
        
    //     if ($player->isNotEmpty()) {
    //         return $player;
    //     }

    //     $player = $this->getPlayersData()
    //         ->filter(function ($player) use ($squadsData) {                                
    //             return Str::afterLast($player->get('name'), ' ') === Str::afterLast($squadsData->get('name'), ' ');
    //         });

    //     if ($player->isNotEmpty()) {
    //         return $player;
    //     }

    //     return collect([['sofa_player_id' => null]]);
    // }
    
    // public function getByPlayerInfo(PlayerInfo $playerInfo): Collection
    // {
    //     return $this->getPlayersData()
    //         ->filter(function ($player) use ($playerInfo) {
    //             return $player->get('name') === $playerInfo->name
    //                 && $player->get('number') === $playerInfo->number;
    //         })
    //         ->whenEmpty(function (Collection $player) use ($playerInfo) {
    //             return $player
    //                 ->filter(function ($player) use ($playerInfo) {
    //                     return $player->get('name') === $playerInfo->name;
    //                 });
    //         })
    //         ->whenEmpty(function ($player) {
    //             return collect(['sofa_player_id' => null]);
    //         });
    // }

    public function getByPlayerInfo(PlayerDataMatcher $matcher)
    {
        return $this->getPlayers()->first(fn ($player) => $matcher->match($player));
    }

    // public function getPlayers(): Collection
    // {
    //     return $this->teamSquad
    //         ->flatten(1)
    //         ->map(function ($group) {
    //             $swapFirstAndLastName = function ($name) {
    //                 $names = collect(Str::of($name)->explode(' '));
                                            
    //                 return $names->reverse()->implode(' ');
    //             };

    //             return collect($group->ITEMS)
    //                 ->map(fn($player) => [
    //                     'id' => $player->PLAYER_ID,
    //                     'name' => $swapFirstAndLastName($player->PLAYER_NAME),
    //                     'number' => $player->PLAYER_JERSEY_NUMBER
    //                 ]);
    //         })
    //         ->flatten(1);
    // }
}