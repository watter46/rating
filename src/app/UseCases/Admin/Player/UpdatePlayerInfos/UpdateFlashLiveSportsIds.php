<?php

namespace App\UseCases\Admin\Player\UpdatePlayerInfos;

use App\Models\PlayerInfo;
use App\UseCases\Admin\FlashLiveSportsRepositoryInterface;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class UpdateFlashLiveSportsIds
{
    public function __construct(private FlashLiveSportsRepositoryInterface $repository)
    {
        
    }

    public function execute()
    {
        try {
            $playerInfos = PlayerInfo::query()
                ->currentSeason()
                ->get();

            $teamSquad = $this->repository->fetchTeamSquad();
                
            if ($playerInfos->isEmpty()) {
                throw new Exception('PlayerInfo data does not exist.');
            }

            $data = $playerInfos
                ->map(function (PlayerInfo $playerInfo) use ($teamSquad) {
                    $player = $teamSquad->getByPlayerInfo(new PlayerDataMatcher($playerInfo));

                    if ($player) {
                        $playerInfo->flash_live_sports_id = $player['id'];
                    }

                    return $playerInfo;
                });
            
            DB::transaction(function () use ($data) {
                $unique = ['id'];
                $updateColumns = ['flash_live_sports_id'];
                
                PlayerInfo::upsert($data->toArray(), $unique, $updateColumns);
            });

        } catch (Exception $e) {
            throw $e;
        }
    }
}