<?php declare(strict_types=1);

namespace App\UseCases\Admin\Player\UpdatePlayerInfos;

use Exception;
use Illuminate\Support\Facades\DB;

use App\Models\PlayerInfo;
use App\UseCases\Admin\FlashLiveSportsRepositoryInterface;


class UpdateFlashLiveSportsIds
{
    public function __construct(
        private PlayerInfo $playerInfo,
        private FlashLiveSportsRepositoryInterface $repository)
    {
        
    }

    public function execute()
    {
        try {
            $teamSquad = $this->repository->fetchTeamSquad();

            $data = $this->playerInfo
                ->playerInfosBuilder()
                ->bulkUpdateFlashLiveSportsData($teamSquad);
            
            DB::transaction(function () use ($data) {
                $unique = PlayerInfo::UPSERT_UNIQUE;
                
                PlayerInfo::upsert($data->toArray(), $unique, PlayerInfo::UPSERT_FLASH_LIVE_SPORTS_COLUMNS);
            });

            $this->playerInfo->playerInfosBuilder()->dispatch($teamSquad);

        } catch (Exception $e) {
            throw $e;
        }
    }
}