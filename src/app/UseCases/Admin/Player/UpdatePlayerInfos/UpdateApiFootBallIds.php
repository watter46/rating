<?php declare(strict_types=1);

namespace App\UseCases\Admin\Player\UpdatePlayerInfos;

use Exception;
use Illuminate\Support\Facades\DB;

use App\Models\PlayerInfo;
use App\UseCases\Admin\ApiFootballRepositoryInterface;


class UpdateApiFootBallIds
{
    public function __construct(
        private PlayerInfo $playerInfo,
        private ApiFootballRepositoryInterface $repository)
    {
        
    }

    public function execute()
    {
        try {
            $squads = $this->repository->fetchSquads();

            $data = $this->playerInfo
                ->playerInfosBuilder()
                ->bulkUpdateApiFootballData($squads);
            
            DB::transaction(function () use ($data) {
                $unique = PlayerInfo::UPSERT_UNIQUE;
                
                PlayerInfo::upsert($data->toArray(), $unique, PlayerInfo::UPSERT_API_FOOTBALL_COLUMNS);
            });

        } catch (Exception $e) {
            throw $e;
        }
    }
}