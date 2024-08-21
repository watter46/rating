<?php declare(strict_types=1);

namespace App\UseCases\Admin\Player;

use Exception;
use Illuminate\Database\Eloquent\Collection;

use App\Models\PlayerInfo;


final readonly class FetchPlayerInfosUseCase
{
    public function execute(): Collection
    {
        try {
            return PlayerInfo::query()
                ->select(['id', 'name', 'number', 'api_player_id'])
                ->currentSeason()
                ->get();

        } catch (Exception $e) {
            throw $e;
        }
    }
}