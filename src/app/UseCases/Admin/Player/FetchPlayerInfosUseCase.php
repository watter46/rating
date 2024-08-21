<?php declare(strict_types=1);

namespace App\UseCases\Admin\Player;

use Exception;
use Illuminate\Database\Eloquent\Collection;

use App\Models\PlayerInfo;
use App\UseCases\Admin\UsersPlayerRating\UpdateUsersPlayerRating;

final readonly class FetchPlayerInfosUseCase
{
    public function execute(): Collection
    {
        try {
            $u = app(UpdateUsersPlayerRating::class);
            $u->execute('01j5t91xbkjkesnv4dx84hqk35');
            
            return PlayerInfo::query()
                ->select(['id', 'name', 'number', 'api_player_id'])
                ->currentSeason()
                ->get();

        } catch (Exception $e) {
            throw $e;
        }
    }
}