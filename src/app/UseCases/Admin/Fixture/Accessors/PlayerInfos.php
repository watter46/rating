<?php declare(strict_types=1);

namespace App\UseCases\Admin\Fixture\Accessors;

use Exception;
use Illuminate\Support\Collection;

use App\Events\PlayerInfosRegistered;
use App\Models\PlayerInfo as PlayerInfoModel;
use App\UseCases\Admin\Fixture\Accessors\Api\ApiPlayer;
use App\UseCases\Admin\Fixture\Accessors\Api\ApiSquad;
use App\UseCases\Admin\Fixture\Accessors\Flash\FlashSquad;
use App\UseCases\Admin\Fixture\Accessors\PlayerInfo;


class PlayerInfos
{    
    /**
     * __construct
     *
     * @param  Collection<PlayerInfo> $playerInfos
     * @return void
     */
    private function __construct(private Collection $playerInfos)
    {
        //
    }

    public static function fromSquad(ApiSquad $squad)
    {
        $models = playerInfoModel::query()
            ->currentSeason()
            ->get();

        $playerInfos = $models
            ->map(fn (PlayerInfoModel $model) => PlayerInfo::fromModel($model))
            ->pipe(function (Collection $playerInfos) use ($squad) {
                $playerIds = $playerInfos
                    ->map(fn (PlayerInfo $playerInfo) => $playerInfo->getPlayerId());

                $unSavedPlayerInfos = $squad->getNotInIds($playerIds)
                    ->pipe(function (Collection $apiPlayers) {
                        if ($apiPlayers->isEmpty()) {
                            return $apiPlayers;
                        }

                        return $apiPlayers
                            ->map(function (ApiPlayer $apiPlayer) {
                                return PlayerInfo::fromApiPlayer($apiPlayer);
                            });
                    });
                
                $updatedPlayerInfos = $playerInfos
                    ->filter(fn(PlayerInfo $playerInfo) => $playerInfo->needsUpdate())
                    ->map(function (PlayerInfo $playerInfo) use ($squad) {
                        $apiPlayer = $squad->getById($playerInfo->getPlayerId());

                        if (!$apiPlayer) {
                            return $playerInfo;
                        }

                        return $playerInfo->updateApiPlayer($apiPlayer);
                    });

                return $unSavedPlayerInfos->merge($updatedPlayerInfos);
            })
            ->values();

        return new self($playerInfos);
    }

    public static function fromFlashSquad(FlashSquad $flashSquad)
    {
        $models = playerInfoModel::query()
            ->currentSeason()
            ->get();
            
        if ($models->isEmpty()) {
            throw new Exception('PlayerInfo model does not exist.');
        }
            
        $playerInfos = $models
            ->map(fn (PlayerInfoModel $model) => PlayerInfo::fromModel($model))
            ->filter(fn (PlayerInfo $playerInfo) => $playerInfo->shouldUpdateFlash())
            ->filter(fn(PlayerInfo $playerInfo) => $flashSquad->exist($playerInfo))
            ->map(function (PlayerInfo $playerInfo) use ($flashSquad) {
                if ($playerInfo->getPlayerId() === 392270) {
                    dd($flashSquad->getByPlayerInfo($playerInfo));
                }
                
                $player = $flashSquad->getByPlayerInfo($playerInfo);

                return $playerInfo->updateFlash($player);
            })
            ->values();

        return new self($playerInfos);
    }

    public function getInvalidImagePlayers()
    {
        return $this->playerInfos
            ->filter(fn (PlayerInfo $playerInfo) => !$playerInfo->hasImage());
    }

    public function upsert(): array
    {
        return $this->playerInfos
            ->map(fn (PlayerInfo $playerInfo) => $playerInfo->toArray())
            ->toArray();
    }

    public function shouldDispatch()
    {
        return $this->playerInfos
            ->some(fn (PlayerInfo $playerInfo) => !$playerInfo->hasImage());
    }
    
    public function dispatch()
    {
        PlayerInfosRegistered::dispatch($this);
    }
}