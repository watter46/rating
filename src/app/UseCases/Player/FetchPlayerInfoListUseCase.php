<?php declare(strict_types=1);

namespace App\UseCases\Player;

use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;

use App\Models\PlayerInfo;
use App\Http\Controllers\Util\PlayerImageFile;


final readonly class FetchPlayerInfoListUseCase
{
    public function __construct(private PlayerImageFile $playerImage)
    {
        //
    }

    public function execute(): Collection
    {
        try {
            return PlayerInfo::query()
                ->select('foot_player_id', 'name', 'number')
                ->currentSeason()
                ->get()
                ->map(function (PlayerInfo $player) {
                    $path = $this->playerImage->generatePath($player->foot_player_id);
                    
                    $player->img = $this->playerImage->getByPath($path);

                    return $player;
                });

        } catch (ModelNotFoundException $e) {
            throw new ModelNotFoundException('PlayerInfo Not Found');

        } catch (Exception $e) {
            throw $e;
        }
    }
}