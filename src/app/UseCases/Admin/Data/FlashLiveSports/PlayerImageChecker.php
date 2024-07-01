<?php declare(strict_types=1);

namespace App\UseCases\Admin\Data\FlashLiveSports;

use Illuminate\Database\Eloquent\Collection;

use App\Http\Controllers\Util\PlayerImageFile;
use App\Models\PlayerInfo;


class PlayerImageChecker
{    
    private PlayerImageFile $file;
    private Collection $playerInfos;
    
    /**
     * __construct
     *
     * @param  TeamSquad $teamSquad
     * @return void
     */
    public function __construct(private TeamSquad $teamSquad)
    {
        $this->file = new PlayerImageFile;
        $this->playerInfos = PlayerInfo::query()
            ->currentSeason(2023)
            ->get(['id', 'name', 'api_football_id', 'flash_live_sports_id']);
    }
    
    /**
     * 保存されているPlayerInfoの画像が存在しているか確認する
     *
     * @param  Collection<PlayerInfo> $playerInfos
     * @return bool
     */
    public function check(): bool
    {
        return $this->playerInfos
            ->filter(fn (PlayerInfo $playerInfo) => !$this->file->exists($playerInfo->api_football_id))
            ->filter(fn (PlayerInfo $playerInfo) => $playerInfo->flash_live_sports_id)
            ->isEmpty();
    }
    
    /**
     * invalidIds
     *
     * @return Collection<PlayerInfo>
     */
    public function invalidPlayerInfos(): Collection
    {
        return $this->playerInfos
            ->filter(fn (PlayerInfo $playerInfo) => !$this->file->exists($playerInfo->api_football_id))
            ->filter(fn (PlayerInfo $playerInfo) => $playerInfo->flash_live_sports_id);
    }
}