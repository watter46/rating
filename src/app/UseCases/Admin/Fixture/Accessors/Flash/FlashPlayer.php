<?php declare(strict_types=1);

namespace App\UseCases\Admin\Fixture\Accessors\Flash;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;

use App\UseCases\Admin\Fixture\Accessors\PlayerName;
use App\UseCases\Admin\Fixture\Accessors\PlayerNumber;


class FlashPlayer
{
    private const TEAM_NAME = 'Chelsea';

    public function __construct(
        private ?PlayerName $name = null,
        private ?PlayerNumber $number = null,
        private ?string $flash_id = null,
        private ?string $flash_image_id = null
    ) {

    }

    public static function create(
        ?PlayerName $name,
        ?PlayerNumber $number,
        ?string $flash_id,
        ?string $flash_image_id): self
    {
        return new self(
            name: $name,
            number: $number,
            flash_id: $flash_id,
            flash_image_id: $flash_image_id
        );
    }

    public static function fromPlayers(Collection $rawPlayersData)
    {
        $player = $rawPlayersData
            ->fromStd()
            ->filter(fn ($player) => self::isChelsea($player['NAME']))
            ->pipe(function (Collection $player) {                
                if ($player->isEmpty()) {
                    return [
                        'name' => null,
                        'number' => null,
                        'flash_id' => null,
                        'flash_image_id' => null
                    ];
                }

                return [
                    'name' => self::toName($player->dataGet('0.NAME', false)),
                    'number' => null,
                    'flash_id' => $player->dataGet('0.ID', false),
                    'flash_image_id' => self::pathToImageId($player->dataGet('0.IMAGE', false))
                ];
            });
        
        return new self(
            $player['name'],
            $player['number'],
            $player['flash_id'],
            $player['flash_image_id']
        );
        
    }

    private static function isChelsea(string $rawName)
    {
        return Str::between($rawName, '(', ')') === self::TEAM_NAME;
    }

    private static function toName(string $rawName)
    {
        $team = '('.self::TEAM_NAME.')';
        
        $name = Str::of($rawName)->remove($team)->squish()->toString();

        return PlayerName::create($name)->swapFirstAndLastName();
    }

    private static function pathToImageId(?string $rawImagePath)
    {
        if (!$rawImagePath) return;
        
        $fileName = Str::afterLast($rawImagePath, '/');

        return Str::beforeLast($fileName, '.png');
    }

    public function getName()
    {
        return $this->name;
    }

    public function getNumber()
    {
        return $this->number;
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