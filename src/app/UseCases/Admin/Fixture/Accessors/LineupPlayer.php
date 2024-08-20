<?php declare(strict_types=1);

namespace App\UseCases\Admin\Fixture\Accessors;

use Illuminate\Support\Collection;

use App\Models\PlayerInfo as PlayerInfoModel;


class LineupPlayer
{
    private function __construct(
        private int $id,
        private PlayerName $name,
        private PlayerNumber $number,
        private PositionType $position,
        private ?string $grid,
        private ?int $goal,
        private ?int $assists,
        private ?string $rating,
        private PlayerInfo $playerInfo
    ) {}

    public static function create(Collection $player)
    {
        $id = $player['id'];
        $name = PlayerName::create($player['name']);
        $number = PlayerNumber::create($player['number']);
        
        return new self(
            $id,
            $name,
            $number,
            $player['position'],
            $player['grid'],
            $player['goal'],
            $player['assists'],
            $player['rating'],
            PlayerInfo::create($name, $number, $id)
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
            PlayerInfo::fromModel($model)
        );
    }

    public function assignPlayerInfo(PlayerInfoModel $model)
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
            PlayerInfo::fromModel($model)->updateIfNeeded($this->playerInfo)
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
        return $this->playerInfo->hasImage();
    }

    public function existPlayerInfo(): bool
    {
        return $this->playerInfo->exist();
    }

    public function isValid(): bool
    {
        return $this->playerInfo->isValid();
    }

    public function needsRegister(): bool
    {
        return $this->playerInfo->needsRegister();
    }
}