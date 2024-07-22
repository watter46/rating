<?php declare(strict_types=1);

namespace App\Livewire\Admin;

use App\Http\Controllers\Util\PlayerImageFile;
use App\Models\PlayerInfo;
use Illuminate\Database\Eloquent\Collection;

class PlayersPresenter
{
    private PlayerImageFile $playerImage;
    
    public function __construct()
    {
        $this->playerImage = new PlayerImageFile;
    }
    
    public function execute(Collection $playerInfos)
    {
        return $playerInfos
            ->map(function (PlayerInfo $playerInfo) {
                $playerInfo->img = [
                    'exists' => $this->playerImage->exists($playerInfo->api_football_id),
                    'img' => $this->playerImage->exists($playerInfo->api_football_id)
                        ? $this->playerImage->generateViewPath($playerInfo->api_football_id)
                        : $this->playerImage->getDefaultPath(),
                    'number' => $playerInfo->number
                ];
                
                return $playerInfo;
            });
    }
}