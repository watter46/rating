<?php declare(strict_types=1);

namespace App\UseCases\Admin\Player\PlayersOfTeamData;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;

use App\Models\PlayerInfo;


readonly class PlayersOfTeamData
{
    private function __construct(private Collection $playersOfTeamData)
    {
        
    }
    
    public static function create(Collection $playersOfTeamData): self
    {
        return new self($playersOfTeamData);
    }

    private function getPlayersData(): Collection
    {
        return collect($this->playersOfTeamData->get('players'))
            ->map(function ($players) {
                return collect([
                    'sofa_player_id' => $players->player->id,
                    'name' => $players->player->name,
                    'number' => $players->player->shirtNumber
                ]);
            });
    }
    
    public function getBySquadsData(Collection $squadsData): Collection
    {        
        $player = $this->getPlayersData()
            ->filter(function ($player) use ($squadsData) {
                return $player->get('name') === $squadsData->get('name')
                    && $player->get('number') === $squadsData->get('number');
            });

        if ($player->isNotEmpty()) {
            return $player;
        }

        $player = $this->getPlayersData()
            ->filter(function ($player) use ($squadsData) {                                
                return Str::afterLast($player->get('name'), ' ') === Str::afterLast($squadsData->get('name'), ' ')
                    && $player->get('number') === $squadsData->get('number');
            });
        
        if ($player->isNotEmpty()) {
            return $player;
        }

        $player = $this->getPlayersData()
            ->filter(function ($player) use ($squadsData) {                                
                return Str::afterLast($player->get('name'), ' ') === Str::afterLast($squadsData->get('name'), ' ');
            });

        if ($player->isNotEmpty()) {
            return $player;
        }

        return collect([['sofa_player_id' => null]]);
    }
    
    public function getByPlayerInfo(PlayerInfo $playerInfo): Collection
    {
        return $this->getPlayersData()
            ->filter(function ($player) use ($playerInfo) {
                return $player->get('name') === $playerInfo->name
                    && $player->get('number') === $playerInfo->number;
            })
            ->whenEmpty(function (Collection $player) use ($playerInfo) {
                return $player
                    ->filter(function ($player) use ($playerInfo) {
                        return $player->get('name') === $playerInfo->name;
                    });
            })
            ->whenEmpty(function ($player) {
                return collect(['sofa_player_id' => null]);
            });
    }
}