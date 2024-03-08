<?php declare(strict_types=1);

namespace App\UseCases\Admin\Player;

use Exception;

use App\Models\PlayerInfo;
use App\Http\Controllers\Util\PlayerImageFile;

final readonly class FetchPlayerInfoUseCase
{
    public function __construct(private PlayerImageFile $playerImage)
    {
        //
    }
    
    public function execute(string $playerInfoId): PlayerInfo
    {
        try {
            /** @var PlayerInfo $player */
            $player = PlayerInfo::find($playerInfoId);

            $path = $this->playerImage->generatePath($player->foot_player_id);
                    
            $player->img = $this->playerImage->getByPath($path);

            return $player;
                        
        } catch (Exception $e) {
            throw $e;
        }
    }
}