<?php declare(strict_types=1);

namespace App\UseCases\Admin\Fixture\Accessors\Flash;

use App\UseCases\Admin\Fixture\Accessors\PlayerInfo;
use App\UseCases\Admin\Fixture\Accessors\PlayerName;
use App\UseCases\Admin\Fixture\Accessors\PlayerNumber;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;


class FlashSquad
{    
    /**
     * __construct
     *
     * @param  Collection<FlashPlayer> $players
     * @return void
     */
    private function __construct(private Collection $players)
    {
        
    }

    public static function create(Collection $rawData)
    {
        $players = $rawData
            ->fromStd()
            ->toCollection()
            ->pluck('ITEMS')
            ->flatten(1)
            ->filter(fn ($player) => $player['PLAYER_TYPE_ID'] !== 'COACH')
            ->map(function (Collection $player) {
                return FlashPlayer::create(
                    PlayerName::create($player['PLAYER_NAME'])->swapFirstAndLastName(),
                    PlayerNumber::create($player['PLAYER_JERSEY_NUMBER']),
                    $player['PLAYER_ID'],
                    self::pathToImageId($player['PLAYER_IMAGE_PATH'])
                );
            });
        
        return new self($players);
    }

    private static function pathToImageId(?string $rawImagePath)
    {
        if (!$rawImagePath) return;
        
        $fileName = Str::afterLast($rawImagePath, '/');

        return Str::beforeLast($fileName, '.png'); 
    }

    public function exist(PlayerInfo $playerInfo)
    {
        $player = $this->players->first(fn (FlashPlayer $flashPlayer) => $playerInfo->match($flashPlayer));

        if (!$player) {
            return false;
        }

        return true;
    }

    public function getByPlayerInfo(PlayerInfo $playerInfo): FlashPlayer
    {
        return $this->players->first(fn (FlashPlayer $flashPlayer) => $playerInfo->match($flashPlayer));
    }
}