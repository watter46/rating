<?php declare(strict_types=1);

namespace App\UseCases\Admin\Player\UpdatePlayerInfos;

use Exception;
use Illuminate\Support\Facades\DB;

use App\Models\PlayerInfo;
use App\UseCases\Admin\ApiFootballRepositoryInterface;
use App\UseCases\Admin\Player\UpdatePlayerInfos\PlayerDataMatcher;


class UpdateApiFootBallIds
{
    public function __construct(private ApiFootballRepositoryInterface $repository)
    {
        
    }

    public function execute()
    {
        try {
            $playerInfos = PlayerInfo::query()
                ->currentSeason()
                ->get();

            $squads = $this->repository->fetchSquads();
                
            if ($playerInfos->isEmpty()) {
                throw new Exception('PlayerInfo data does not exist.');
            }

            $data = $playerInfos
                ->map(function (PlayerInfo $playerInfo) use ($squads) {
                    $player = $squads->getByPlayerInfo(new PlayerDataMatcher($playerInfo));

                    if ($player) {
                        $playerInfo->foot_player_id = $player['id'];
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