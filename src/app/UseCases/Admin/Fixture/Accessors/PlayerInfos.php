<?php declare(strict_types=1);

namespace App\UseCases\Admin\Fixture\Accessors;

use Exception;
use Illuminate\Support\Collection;

use App\Events\PlayerInfosRegistered;
use App\Models\PlayerInfo as PlayerInfoModel;
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

    public static function fromSquad(Squad $squad)
    {
        $models = playerInfoModel::query()
            ->currentSeason()
            ->get();

        $playerInfos = $models
            ->map(fn (PlayerInfoModel $model) => PlayerInfo::fromModel($model))
            ->pipe(function (Collection $playerInfos) use ($squad) {
                $playerIds = $playerInfos
                    ->map(function (PlayerInfo $playerInfo) {
                        return $playerInfo->getPlayerId();
                    });

                $notInPlayers = $squad->getNotInIds($playerIds)
                    ->pipe(function (Collection $players) {
                        if ($players->isEmpty()) {
                            return $players;
                        }

                        return $players
                            ->map(function (array $player) {
                                return PlayerInfo::create(
                                    PlayerName::create($player['name']),
                                    PlayerNumber::create($player['number']),
                                    $player['id']
                                );
                            });
                    });
                
                $updatedPlayers = $playerInfos
                    ->filter(fn(PlayerInfo $playerInfo) => $playerInfo->needsUpdate())
                    ->map(function (PlayerInfo $playerInfo) use ($squad) {
                        $player = $squad->getById($playerInfo->getPlayerId());

                        if (!$player) {
                            return $playerInfo;
                        }

                        return $playerInfo
                            ->updateIfShortenName(PlayerName::create($player['name']))
                            ->updateIfDifferentNumber(PlayerNumber::create($player['number']));
                    });

                return $notInPlayers->merge($updatedPlayers);
            })
            ->values();

        return new self($playerInfos);
    }

    public static function create(FlashSquad $flashPlayers)
    {
        $models = playerInfoModel::query()
            ->currentSeason()
            ->get();

        if ($models->isEmpty()) {
            throw new Exception('PlayerInfo model does not exist.');
        }
            
        $playerInfos = $models
            ->map(fn (PlayerInfoModel $model) => PlayerInfo::fromModel($model))
            ->filter(fn (PlayerInfo $playerInfo) => $playerInfo->needsUpdate())
            ->map(function (PlayerInfo $playerInfo) use ($flashPlayers) {
                $player = $flashPlayers
                    ->getAll()
                    ->first(fn ($player) => $playerInfo->match($player));

                if (!$player) {
                    return $playerInfo;
                }

                if ($playerInfo->isShortenName()) {
                    return $playerInfo
                        ->updateName($player['name'])
                        ->updateFlashId($player['flash_id'])
                        ->updateFlashImageId($player['flash_image_id']);
                }

                return $playerInfo
                    ->updateFlashId($player['flash_id'])
                    ->updateFlashImageId($player['flash_image_id']);
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