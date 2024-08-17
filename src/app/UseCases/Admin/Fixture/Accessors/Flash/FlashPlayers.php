<?php declare(strict_types=1);

namespace App\UseCases\Admin\Fixture\Accessors\Flash;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;


class FlashPlayers
{
    private const TEAM_NAME = 'Chelsea';

    public function __construct(private ?string $flash_id, private ?string $flash_image_id)
    {
        dd($flash_id, $flash_image_id);
    }

    public static function create(Collection $rawData): self
    {
        $isChelsea = fn ($name) => Str::between($name, '(', ')') === self::TEAM_NAME;
        
        return $rawData
            ->filter(fn ($player) => $isChelsea($player->NAME))
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

                return new self(
                    flash_id: $player[0]->ID,
                    flash_image_id: $player[0]->IMAGE
                        ? $pathToImageId($player[0]->IMAGE)
                        : null
                );
            });
    }
}