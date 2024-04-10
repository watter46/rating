<?php declare(strict_types=1);

namespace App\UseCases\Admin\Player\SquadsData;

use Illuminate\Support\Collection;

use App\Models\PlayerInfo;
use App\UseCases\Admin\Player\PlayersOfTeamData\PlayersOfTeamData;
use App\UseCases\Util\Season;


readonly class SquadsData
{
    private function __construct(private Collection $squadsData)
    {
        //
    }
    
    public static function create(Collection $squadsData): self
    {
        return new self($squadsData);
    }

    private function getPlayersData(): Collection
    {
        return collect($this->squadsData->get('players'))
            ->map(function ($player) {
                return collect([
                    'foot_player_id' => $player->id,
                    'name'   => $player->name,
                    'number' => $player->number,
                    'season' => Season::current()
                ]);
            });
    }

    public function getByPlayerInfo(PlayerInfo $playerInfo): Collection
    {
        return $this->getPlayersData()
            ->filter(function (Collection $player) use ($playerInfo) {
                return $player->get('name') === $playerInfo->name
                    && $player->get('number') === $playerInfo->number;
            })
            ->whenEmpty(function (Collection $player) use ($playerInfo) {
                return $player
                    ->filter(function ($player) use ($playerInfo) {
                        return $player->get('name') === $playerInfo->name;
                    });
            });
    }

    public function merge(PlayersOfTeamData $playersOfTeamData): Collection
    {        
        return $this->getPlayersData()
            ->map(function ($player) use ($playersOfTeamData) {
                return $player->merge(
                    $playersOfTeamData
                        ->getBySquadsData($player->only(['name', 'number']))
                        ->first()
                );
            });
    }
}