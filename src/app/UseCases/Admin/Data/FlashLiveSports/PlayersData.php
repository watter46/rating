<?php declare(strict_types=1);

namespace App\UseCases\Admin\Data\FlashLiveSports;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;


class PlayersData
{
    private const TEAM_NAME = 'Chelsea';

    private function __construct(private ?string $flashId, private ?string $flashImageId)
    {
        //
    }

    public static function create(Collection $playersData): self
    {
        $isChelsea = fn($player) => Str::between($player->NAME, '(', ')') === self::TEAM_NAME;
        
        $player = $playersData
            ->filter(fn ($player) => $isChelsea($player))
            ->values()
            ->pipe(function (Collection $players) {
                if ($players->isEmpty()) {
                    return [
                        'id' => null,
                        'imageId' => null
                    ];
                }

                $pathToImageId = function (string $path) {
                    $png = Str::afterLast($path, '/');

                    return Str::beforeLast($png, '.png');
                };
                
                return [
                    'id' => $players[0]->ID,
                    'imageId' => $players[0]->IMAGE
                        ? $pathToImageId($players[0]->IMAGE)
                        : null
                ];
            });
        
        return new self($player['id'], $player['imageId']);
    }

    public function getFlashId()
    {
        return $this->flashId;
    }

    public function getFlashImageId()
    {
        return $this->flashImageId;
    }
}