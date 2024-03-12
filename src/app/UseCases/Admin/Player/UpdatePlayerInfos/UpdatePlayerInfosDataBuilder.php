<?php declare(strict_types=1);

namespace App\UseCases\Admin\Player\UpdatePlayerInfos;

use Illuminate\Support\Collection;

use App\Models\PlayerInfo;
use App\UseCases\Admin\Player\UpdatePlayerInfos\SquadsData;
use App\UseCases\Admin\Player\UpdatePlayerInfos\PlayersOfTeamData;


readonly class UpdatePlayerInfosDataBuilder
{
    public function __construct(private PlayerMatcher $playerMatcher)
    {
        //
    }

    public function build(SquadsData $squadsData, PlayersOfTeamData $playersOfTeamData, Collection $playerInfos)
    {
        // SofaScoreとApiFootballのプレイヤーデータを統合する
        $mergedData = $squadsData->getData()
            ->map(function (array $squadsDataPlayer) use ($playersOfTeamData) {
                $playersOfTeamDataPlayer = $playersOfTeamData->findOrNull($squadsDataPlayer);
                    
                if (!$playersOfTeamDataPlayer) {
                    return array_merge($squadsDataPlayer, ['sofa_player_id' => null]);
                }

                return array_merge($squadsDataPlayer, $playersOfTeamDataPlayer);
            });
        
        if ($playerInfos->isEmpty()) {
            return $mergedData->toArray();
        }

        // PlayerInfoに統合したAPIデータを統合する
        $result = $playerInfos
            ->map(function (PlayerInfo $playerInfo) use ($mergedData) {
                $playerData = $mergedData
                    ->first(function ($playerData) use ($playerInfo) {
                        return $this->playerMatcher->isMatch($playerData, $playerInfo->toArray());
                    });

                if (!$playerData) {
                    return $playerInfo->toArray();
                }

                return array_merge($playerInfo->toArray(), $playerData);
            })
            ->toArray();

        return $result;
    }
}