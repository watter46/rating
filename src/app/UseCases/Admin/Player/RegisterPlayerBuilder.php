<?php declare(strict_types=1);

namespace App\UseCases\Admin\Player;

use Exception;
use Illuminate\Support\Collection;

use App\Models\PlayerInfo;
use App\UseCases\Admin\Player\PlayerData\PlayerData;


final readonly class RegisterPlayerBuilder
{    
    /**
     * build
     *
     * @property Collection<PlayerData> $playersData
     * @property Collection<PlayerInfo> $playerInfos
     * @return array
     */
    public function build(Collection $playersData, Collection $playerInfos)
    {
        try {
            return $playersData
                ->map(function (PlayerData $playerData) use ($playerInfos) {
                    $playerInfo = $playerInfos
                        ->keyBy('foot_player_id')
                        ->get($playerData->getFootPlayerId());

                    if (!$playerInfo?->id) {
                        return $playerData->getPlayerData();
                    }
                    
                    return $playerData->getPlayerData()->merge(['id' => $playerInfo->id]);
                })
                ->toArray();

        } catch (Exception $e) {
            throw $e;
        }
    }
}