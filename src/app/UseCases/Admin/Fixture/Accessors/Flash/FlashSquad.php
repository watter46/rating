<?php declare(strict_types=1);

namespace App\UseCases\Admin\Fixture\Accessors\Flash;

use App\Models\PlayerInfo as PlayerInfoModel;
use App\UseCases\Admin\Fixture\Accessors\Player;
use App\UseCases\Admin\Fixture\Accessors\PlayerInfo;
use App\UseCases\Admin\Fixture\Accessors\PlayerName;
use App\UseCases\Admin\Fixture\Accessors\PlayerNumber;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;


class FlashSquad
{    
    /**
     * __construct
     *
     * @param  Collection<array{
     *  flash_id: string,
     *  name: PlayerName,
     *  number: PlayerNumber,
     *  flash_image_id: ?string
     * }> $players
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
                $pathToImageId = function (string $path) {
                    $png = Str::afterLast($path, '/');

                    return Str::beforeLast($png, '.png');
                };

                return [
                    'flash_id' => $player['PLAYER_ID'],
                    'name'     => PlayerName::create($player['PLAYER_NAME'])->swapFirstAndLastName(),
                    'number'   => PlayerNumber::create($player['PLAYER_JERSEY_NUMBER']),
                    'flash_image_id' => $player['PLAYER_IMAGE_PATH']
                        ? $pathToImageId($player['PLAYER_IMAGE_PATH'])
                        : null
                ];
            });
        
        return new self($players);
    }

    public function getAll()
    {
        return $this->players;
    }
}