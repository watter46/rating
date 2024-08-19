<?php declare(strict_types=1);

namespace App\UseCases\Admin\Fixture\Accessors\Flash;

use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;


class FlashPlayer
{
    private const TEAM_NAME = 'Chelsea';

    public function __construct(private ?string $flash_id, private ?string $flash_image_id)
    {
        //
    }

    // public static function fromPlayer(Collection $rawPlayerData): self
    // {

    //     return new self(
    //         // flash_id: $player[0]->ID,
    //         // flash_image_id: $player[0]->IMAGE
    //         //     ? $pathToImageId($player[0]->IMAGE)
    //         //     : null
    //     );
    // }

    public static function fromPlayers(Collection $rawPlayersData)
    {
        $isChelsea = fn ($name) => Str::between($name, '(', ')') === self::TEAM_NAME;
    
        $player = $rawPlayersData
            ->filter(fn ($player) => $isChelsea($player->NAME))
            ->pipe(function (Collection $player) {
                if ($player->isEmpty()) {
                    return [
                        'flash_id' => null,
                        'flash_image_id' => null
                    ];
                }

                $pathToImageId = function (string $path) {
                    $png = Str::afterLast($path, '/');

                    return Str::beforeLast($png, '.png');
                };

                return [
                    'flash_id' => $player->first()->ID,
                    'flash_image_id' => $player->first()->IMAGE
                        ? $pathToImageId($player->first()->IMAGE)
                        : null
                ];
            });
        
        return new self(
            $player['flash_id'],
            $player['flash_image_id']
        );
        
    }

    public function getFlashId()
    {
        return $this->flash_id;
    }

    public function getFlashImageId()
    {
        return $this->flash_image_id;
    }
}