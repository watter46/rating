<?php declare(strict_types=1);

namespace App\UseCases\Admin\Fixture;

use Exception;
use Illuminate\Support\Facades\DB;

use App\Models\PlayerInfo;
use App\UseCases\Admin\FlashLiveSportsRepositoryInterface;


class UpdateFlashIds
{
    public function __construct(private FlashLiveSportsRepositoryInterface $repository)
    {
        
    }

    public function execute()
    {
        try {
            $playerInfos = $this->repository->fetchSquad();
            
            DB::transaction(function () use ($playerInfos) {
                $unique = PlayerInfo::UPSERT_UNIQUE;
                
                PlayerInfo::upsert($playerInfos->upsert(), $unique, PlayerInfo::UPSERT_FLASH_COLUMNS);
            });
            
            if ($playerInfos->shouldDispatch()) {
                $playerInfos->dispatch();
            }

        } catch (Exception $e) {
            throw $e;
        }
    }
}