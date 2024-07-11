<?php declare(strict_types=1);

namespace App\UseCases\Admin\Player;

use Exception;
use Illuminate\Database\Eloquent\Collection;

use App\Models\PlayerInfo;
use App\UseCases\Admin\Player\UpdatePlayerInfos\UpdateApiFootBallIds;
use App\UseCases\Admin\Player\UpdatePlayerInfos\UpdateFlashLiveSportsIds;

final readonly class FetchPlayerInfosUseCase
{
    public function __construct(private UpdateFlashLiveSportsIds $u)
    {
        
    }
    
    public function execute(): Collection
    {
        try {
            $this->u->execute();
            
            return PlayerInfo::query()
                ->select(['id', 'name', 'number', 'api_football_id'])
                ->currentSeason()
                ->get();

        } catch (Exception $e) {
            throw $e;
        }
    }
}