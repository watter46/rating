<?php declare(strict_types=1);

namespace App\UseCases\Admin\Fixture\Accessors;

use Illuminate\Support\Collection;

use App\Models\PlayerInfo as playerInfoModel;
use App\Http\Controllers\Util\PlayerImageFile;
use App\UseCases\Admin\Fixture\Accessors\Api\ApiPlayer;
use App\UseCases\Admin\Fixture\Accessors\Flash\FlashPlayer;
use App\UseCases\Admin\Fixture\PlayerMatcher;
use App\UseCases\Util\Season;


class PlayerInfo
{
    private PlayerImageFile $image;
    private PlayerMatcher $matcher;

    private function __construct(
        private PlayerStatusType $status,
        private ?string $id = null,
        private ?PlayerName $name = null,
        private ?PlayerNumber $number = null,
        private ?int $season = null,
        private ?int $api_player_id = null,
        private ?string $flash_id = null,
        private ?string $flash_image_id = null
    ) {
        $this->image   = new PlayerImageFile;
        $this->matcher = new PlayerMatcher($name, $number);
    }

    public static function fromApiPlayer(ApiPlayer $apiPlayer)
    {
        return (new self(
            name: $apiPlayer->getName(),
            number: $apiPlayer->getNumber(),
            season: Season::current(),
            api_player_id: $apiPlayer->getId(),
            status: PlayerStatusType::Created
        ))
        ->updateStatus();
    }
    
    public static function create(PlayerName $name, PlayerNumber $number, int $api_player_id)
    {
        return (new self(
            name: $name,
            number: $number,
            season: Season::current(),
            api_player_id: $api_player_id,
            status: PlayerStatusType::Created
        ))
        ->updateStatus();
    }
    
    public static function fromModel(playerInfoModel $model): self
    {
        return (new self(
            PlayerStatusType::Created,
            $model->id,
            PlayerName::create($model->name),
            PlayerNumber::create($model->number),
            $model->season,
            $model->api_player_id,
            $model->flash_id,
            $model->flash_image_id
        ))
        ->updateStatus();
    }

    public function updateIfNeeded(PlayerInfo $playerInfo)
    {
        $name = $this->isShortenName() ? $playerInfo->getName() : $this->name;
        $number = !$this->equalNumber($playerInfo->getNumber()) ? $playerInfo->getNumber() : $this->number;

        return $this->setAttribute(
            status: PlayerStatusType::Updated,
            name: $name,
            number: $number,
            season: Season::current()
        )
        ->updateStatus();
    }
    
    public function updateApiPlayer(ApiPlayer $apiPlayer)
    {
        return $this->setAttribute(
            status: PlayerStatusType::Updated,
            name: $this->isShortenName() ? $apiPlayer->getName() : $this->name,
            number: !$this->equalNumber($apiPlayer->getNumber()) ? $apiPlayer->getNumber() : $this->number,
            api_player_id: $apiPlayer->getId()
        );
    }
    
    public function updateFlash(FlashPlayer $flashPlayer)
    {
        return $this->setAttribute(
            status: PlayerStatusType::Updated,
            name: $this->isShortenName() ? $flashPlayer->getName() : $this->name,
            flash_id: $flashPlayer->getFlashId(),
            flash_image_id: $flashPlayer->getFlashImageId(),
        );
    }

    public function exist(): bool
    {
        return !is_null($this->id);
    }

    public function isShortenName(): bool
    {
        return $this->name->isShorten();
    }

    public function equalNumber(PlayerNumber $number): bool
    {
        return $this->number->equal($number);
    }
    
    public function shouldUpdate(PlayerNumber $number): bool
    {
        return $this->isShortenName() || !$this->equalNumber($number);
    }

    public function shouldFetchFlash(): bool
    {
        return !$this->flash_id;
    }

    public function isUpdated()
    {
        return $this->status->isUpdated();
    }

    public function shouldUpdateFlash()
    {
        return $this->shouldFetchFlash() || $this->shouldUpdate($this->number);
    }

    public function match(FlashPlayer $flashPlayer)
    {
        return $this->matcher->match($flashPlayer);
    }

    public function needsRegister(): bool
    {
        return $this->status->needsRegister();
    }

    public function needsUpdate(): bool
    {
        return $this->status->needsUpdate();
    }

    public function isValid(): bool
    {
        return $this->status->isValid();
    }

    public function hasImage(): bool
    {
        if (!$this->flash_image_id) {
            return true;
        }

        return $this->image->exists($this->api_player_id);
    }

    public function buildFill(): array
    {
        return [
            $this->name->getFullName(),
            $this->number->get(),
            $this->season,
            $this->api_player_id,
            $this->flash_id,
            $this->flash_image_id,
        ];
    }

    public function toModel()
    {
        return new playerInfoModel($this->toArray());
    }

    public function toArray()
    {
        return collect([
            'id' => $this->id,
            'name' => $this->name->getFullName(),
            'number' => $this->number->get(),
            'season' => $this->season,
            'api_player_id' => $this->api_player_id,
            'flash_id' => $this->flash_id,
            'flash_image_id' => $this->flash_image_id
        ])
        ->pipe(function (Collection $playerInfo) {
            if ($playerInfo['id']) {
                return $playerInfo;
            }

            return $playerInfo->forget('id');
        })
        ->toArray();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getFlashPlayerId()
    {
        return $this->flash_id;
    }

    public function getImageId(): ?string
    {
        return $this->flash_image_id;
    }

    public function getPlayerId()
    {
        return $this->api_player_id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getNumber()
    {
        return $this->number;
    }

    public function updatePlayerId(int $apiPlayerId): self
    {
        return $this->setAttribute(api_player_id: $apiPlayerId, status: PlayerStatusType::Updated);
    }

    public function updateName(PlayerName $name): self
    {
        return $this->setAttribute(name:$name, status: PlayerStatusType::Updated);
    }

    public function updateNumber(PlayerNumber $number): self
    {
        return $this->setAttribute(number:$number, status: PlayerStatusType::Updated);
    }

    public function updateFlashId(string $flashId): self
    {
        return $this->setAttribute(flash_id: $flashId, status: PlayerStatusType::Updated);
    }

    public function updateFlashImageId(string $flashImageId): self
    {
        return $this->setAttribute(flash_image_id: $flashImageId, status: PlayerStatusType::Updated);
    }

    public function updateSeason(): self
    {
        return $this->setAttribute(season: Season::current(), status: PlayerStatusType::Updated);
    }

    private function getStatus()
    {
        if (!$this->exist()) {
            return PlayerStatusType::NeedsRegister;
        }

        if ($this->shouldFetchFlash()) {
            return PlayerStatusType::NeedsFetchFlash;
        }

        if ($this->shouldUpdate($this->number)) {
            return PlayerStatusType::NeedsUpdate;
        }

        if ($this->status->isUpdated()) {
            return $this->status;
        }

        return PlayerStatusType::Valid;
    }

    private function updateStatus()
    {
        return $this->setAttribute(status: $this->getStatus());
    }

    private function setAttribute(
        PlayerStatusType $status,
        ?string $id = null,
        ?PlayerName $name = null,
        ?PlayerNumber $number = null,
        ?int $season = null,
        ?int $api_player_id = null,
        ?string $flash_id = null,
        ?string $flash_image_id = null): self
    {
        return new self(
            status: $status,
            id: $id ?? $this->id,
            name: $name ?? $this->name,
            number: $number ?? $this->number,
            season: $season ?? $this->season,
            api_player_id: $api_player_id ?? $this->api_player_id,
            flash_id: $flash_id ?? $this->flash_id,
            flash_image_id: $flash_image_id ?? $this->flash_image_id
        );
    }
}