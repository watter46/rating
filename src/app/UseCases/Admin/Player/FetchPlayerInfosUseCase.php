<?php declare(strict_types=1);

namespace App\UseCases\Admin\Player;

use Exception;
use Illuminate\Database\Eloquent\Collection;

use App\Models\PlayerInfo;
use App\UseCases\Admin\Player\UpdatePlayerInfos\UpdateFlashIds;

final readonly class FetchPlayerInfosUseCase
{
    public function execute(): Collection
    {
        try {
            $update = app(UpdateFlashIds::class);
            $update->execute();
            
            return PlayerInfo::query()
                ->select(['id', 'name', 'number', 'api_player_id'])
                ->currentSeason()
                ->get();

        } catch (Exception $e) {
            throw $e;
        }
    }
}