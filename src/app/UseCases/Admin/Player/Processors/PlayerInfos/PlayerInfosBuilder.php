<?php declare(strict_types=1);

namespace App\UseCases\Admin\Player\Processors\PlayerInfos;

use Exception;
use Illuminate\Support\Collection;

use App\Events\PlayerInfoRegistered;
use App\Models\PlayerInfo;
use App\UseCases\Admin\Data\ApiFootball\SquadsData;
use App\UseCases\Admin\Data\FlashLiveSports\TeamSquad;
use App\UseCases\Admin\Player\Processors\PlayerInfos\PlayerDataMatcher;


class PlayerInfosBuilder
{
    private TeamSquad $teamSquad;
    
    private function __construct(private Collection $playerInfos)
    {
        //
    }

    public static function create(): self
    {
        return new self(
            PlayerInfo::query()
                ->currentSeason()
                ->get()
        );
    }

    public function bulkUpdateApiFootballData(SquadsData $squads)
    {
        return $this->playerInfos
            ->map(function (PlayerInfo $playerInfo) use ($squads) {
                $player = $squads->getByPlayerInfo(new PlayerDataMatcher($playerInfo));

                if ($player) {
                    $playerInfo->api_football_id = $player['id'];
                }

                return $playerInfo;
            });
    }

    public function bulkUpdateFlashLiveSportsData(TeamSquad $teamSquad)
    {
        if ($this->playerInfos->isEmpty()) {
            throw new Exception('PlayerInfo data does not exist.');
        }
        
        return $this->playerInfos
            ->map(function (PlayerInfo $playerInfo) use ($teamSquad) {
                $player = $teamSquad->getByPlayerInfo(new PlayerDataMatcher($playerInfo));
                
                if ($player) {
                    $playerInfo->flash_live_sports_id = $player['id'];
                    $playerInfo->flash_live_sports_image_id = $player['imageId'];
                }

                return $playerInfo;
            });
    }

    /**
     * getTeamImageIds
     *
     * @return Collection<PlayerInfo>
     */
    public function getInvalidPlayerImageIds(): Collection
    {
        return $this->teamSquad->getInvalidPlayerInfos();
    }

    /**
     * 試合で使用するデータがすべて存在するか確認して
     * 存在しない場合、不足しているデータを取得するイベントを発行する
     *
     * @param  TeamSquad $teamSquad
     * @return void
     */
    public function dispatch(TeamSquad $teamSquad)
    {
        if ($teamSquad->check()) return;

        $this->teamSquad = $teamSquad;
            
        PlayerInfoRegistered::dispatch($this);
    }
}