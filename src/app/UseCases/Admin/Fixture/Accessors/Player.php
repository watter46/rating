<?php declare(strict_types=1);

namespace App\UseCases\Admin\Fixture\Accessors;

use Illuminate\Support\Collection;

use App\Http\Controllers\Util\PlayerImageFile;
use App\Models\PlayerInfo as PlayerInfoModel;
use App\UseCases\Admin\Fixture\Accessors\PlayerInfo;
use App\UseCases\Admin\Data\ApiFootball\FixtureData\PositionType;


class Player
{
    private PlayerImageFile $image;
    
    public function __construct(
        private int $id,
        private PlayerName $name,
        private PlayerNumber $number,
        private PositionType $position,
        private ?string $grid,
        private ?int $goal,
        private ?int $assists,
        private ?string $rating,
        private PlayerInfo $playerInfo = new PlayerInfo
    ) {
        $this->image = new PlayerImageFile;
    }

    public static function create(Collection $player)
    {
        return new self(
            $player['id'],
            PlayerName::create($player['name']),
            PlayerNumber::create($player['number']),
            $player['position'],
            $player['grid'],
            $player['goal'],
            $player['assists'],
            $player['rating'],
            PlayerInfo::create()
        );
    }

    public static function reconstruct(Collection $player, PlayerInfoModel $model)
    {
        return new self(
            $player['id'],
            PlayerName::create($player['name']),
            PlayerNumber::create($player['number']),
            $player['position'],
            $player['grid'],
            $player['goal'],
            $player['assists'],
            $player['rating'],
            PlayerInfo::create($model)
        );
    }

    public function assignPlayerInfo(PlayerInfoModel $playerInfoModel)
    {
        return new self(
            $this->id,
            $this->name,
            $this->number,
            $this->position,
            $this->grid,
            $this->goal,
            $this->assists,
            $this->rating,
            PlayerInfo::create($playerInfoModel)
        );
    }

    public function toModel()
    {
        return [
            'id'       => $this->id,
            'name'     => $this->name->getFullName(),
            'number'   => $this->number->get(),
            'position' => $this->position->value,
            'grid'     => $this->grid,
            'goal'     => $this->goal,
            'assists'  => $this->assists,
            'rating'   => $this->rating,
        ];
    }

    public function getPlayerId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name->getFullName();
    }

    public function getPlayerInfo(): PlayerInfo
    {
        return $this->playerInfo;
    }

    public function hasImage(): bool
    {
        if (!$this->playerInfo->getImageId()) {
            return true;
        }

        return $this->image->exists($this->getPlayerId());
    }

    public function existPlayerInfo(): bool
    {
        return $this->playerInfo->exist();
    }

    public function isValid(): bool
    {
        return $this->playerInfo->isValid();
    }

    public function isNeedsRegister(): bool
    {
        return $this->playerInfo->isNeedsRegister();
    }

    public function toPlayerData()
    {
        return [
            'apiPlayerId' => $this->id,
            'name' => $this->name,
            'number' => $this->number
        ];
    }
}