<?php declare(strict_types=1);

namespace App\UseCases\Admin\Player\UpdatePlayerInfos;

use Exception;
use Illuminate\Support\Facades\DB;

use App\Models\PlayerInfo;
use App\UseCases\Admin\FlashLiveSportsRepositoryInterface;


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
                $unique = PlayerInfo::UPSERT_UNIQUE;
                
                PlayerInfo::upsert($data->toArray(), $unique);
            });

            PlayerInfo::upserted($teamSquad);

        } catch (Exception $e) {
            throw $e;
        }
    }
}