<?php declare(strict_types=1);

namespace App\UseCases\Admin\Data\FlashLiveSports;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;


class PlayersData
{
    private const TEAM_NAME = 'Chelsea';

    public function __construct(private Collection $playersData)
    {
        
    }

    public static function create(Collection $playersData): self
    {
        return new self($playersData);
    }

    public function get()
    {
        return $this->playersData
            ->filter(fn ($player) => $this->isChelsea($player->NAME))
            ->pipe(function (Collection $player) {
                if ($player->isEmpty()) {
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
                    'id' => $player[0]->ID,
                    'imageId' => $player[0]->IMAGE
                        ? $pathToImageId($player[0]->IMAGE)
                        : null
                ];
            });
    }

    private function isChelsea(string $name): bool
    {
        return Str::between($name, '(', ')') === self::TEAM_NAME;
    }

    public function imagePaths(): array
    {
        return $this->playersData
            ->filter(fn ($player) => $this->isChelsea($player->NAME))
            ->pipe(function (Collection $player) {
                if ($player->isEmpty()) {
                    return [
                        'id' => null,
                        'path' => null
                    ];
                }

                dd($player[0]);
                
                // return [
                //     'id' => $player[0]->ID,
                //     'path' => $player[0]->IMAGE
                // ];
            });

        // $invalidPlayerInfos = $this->checker->invalidPlayerInfos();

        // $invalidData = $this->teamSquad
        //     ->flatten(1)
        //     ->map(function ($group) {
        //         return collect($group->ITEMS)
        //             ->map(fn($player) => [
        //                 'id' => $player->PLAYER_ID,
        //                 'path' => $player->PLAYER_IMAGE_PATH
        //             ]);
        //     })
        //     ->flatten(1)
        //     ->whereIn('id', $invalidPlayerInfos->pluck('flash_live_sports_id')->toArray())
        //     ->keyBy('id');

        // return $invalidPlayerInfos
        //     ->map(function (PlayerInfo $playerInfo) use ($invalidData) {
        //         $playerInfo->path = $invalidData->get($playerInfo->flash_live_sports_id)['path'];

        //         return $playerInfo;
        //     });
    }
}