<?php declare(strict_types=1);

namespace App\UseCases\Admin\Player;

use Illuminate\Support\Collection;

use App\Models\PlayerInfo;
use App\UseCases\Admin\Player\SquadsData\SquadsData;
use App\UseCases\Admin\Player\PlayersOfTeamData\PlayersOfTeamData;


readonly class UpdatePlayerInfosDataBuilder
{
    public function __construct(private PlayerInfo $playerInfo)
    {
        
    }
    
    public function build(SquadsData $squadsData, PlayersOfTeamData $playersOfTeamData): Collection
    {
        $playerInfos = $this->playerInfo
            ->currentSeason()
            ->get();
        
        if ($playerInfos->isEmpty()) {
            return $squadsData->merge($playersOfTeamData);
        }

        return $playerInfos
            ->map(function (PlayerInfo $playerInfo) use ($squadsData, $playersOfTeamData) {
                return collect($playerInfo)
                    ->merge($squadsData->getByPlayerInfo($playerInfo)->first())
                    ->merge($playersOfTeamData->getByPlayerInfo($playerInfo)->first());
            });
    }
}