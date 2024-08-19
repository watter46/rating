<?php declare(strict_types=1);

namespace App\UseCases\Admin\Fixture\Accessors;

use Illuminate\Support\Collection;

use App\Models\PlayerInfo as playerInfoModel;
use App\Http\Controllers\Util\PlayerImageFile;
use App\UseCases\Admin\Fixture\Accessors\Flash\FlashPlayer;
use App\UseCases\Admin\Fixture\PlayerMatcher;
use App\UseCases\Util\Season;


class PlayerInfo
{
    private PlayerImageFile $image;
    private PlayerStatusType $status;

    private function __construct(
        private ?string $id = null,
        private ?PlayerName $name = null,
        private ?PlayerNumber $number = null,
        private ?int $season = null,
        private ?int $api_player_id = null,
        private ?string $flash_id = null,
        private ?string $flash_image_id = null
    ) {
        $this->status = $this->updateStatus();
        $this->image = new PlayerImageFile;
    }

    public function st()
    {
        return $this->status->name;
    }
    
    // ifを消せるか

    public static function create(playerInfoModel $model = new playerInfoModel): self
    {
        if (!$model->id) {
            return new self;
        }
        
        return new self(
            $model->id,
            PlayerName::create($model->name),
            PlayerNumber::create($model->number),
            $model->season,
            $model->api_player_id,
            $model->flash_id,
            $model->flash_image_id,
        );
    }

    public static function fromPlayer(PlayerName $name, PlayerNumber $number, int $api_player_id)
    {
        return new self(
            name: $name,
            number: $number,
            season: Season::current(),
            api_player_id: $api_player_id
        );
    }

    public function updateFlash(FlashPlayer $flashPlayer)
    {
        return $this->setAttribute(
            flash_id: $flashPlayer->getFlashId(),
            flash_image_id: $flashPlayer->getFlashImageId()
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

    public function match(array $player)
    {
        $matcher = new PlayerMatcher($this->name, $this->number);

        return $matcher->match($player);
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
        return $this->name->getFullName();
    }

    public function updateFromPlayer(LineupPlayer $player): self
    {        
        ['name' => $name, 'number' => $number] = $player->toPlayerData();

        return $this->setAttribute(name: $name, number: $number, season: Season::current());
    }

    public function updatePlayerId(int $apiPlayerId): self
    {
        return $this->setAttribute(api_player_id: $apiPlayerId);
    }

    public function updateName(PlayerName $name): self
    {
        return $this->setAttribute(name:$name);
    }

    public function updateNumber(PlayerNumber $number): self
    {
        return $this->setAttribute(number:$number);
    }

    public function updateFlashId(string $flashId): self
    {
        return $this->setAttribute(flash_id: $flashId);
    }

    public function updateFlashImageId(string $flashImageId): self
    {
        return $this->setAttribute(flash_image_id: $flashImageId);
    }

    public function updateSeason(): self
    {
        return $this->setAttribute(season: Season::current());
    }

    public function updateIfShortenName(PlayerName $name)
    {
        if (!$this->isShortenName()) {
            return $this;
        }

        return $this->updateName($name);
    }

    public function updateIfDifferentNumber(PlayerNumber $number)
    {
        if ($this->equalNumber($number)) {
            return $this;
        }

        return $this->updateNumber($number);
    }

    private function updateStatus(): PlayerStatusType
    {
        if (!$this->exist()) {
            return PlayerStatusType::NeedsRegister;
        }

        if ($this->shouldUpdate($this->number)) {
            return PlayerStatusType::NeedsUpdate;
        }

        if ($this->shouldFetchFlash()) {
            return PlayerStatusType::NeedsFetchFlash;
        }

        return PlayerStatusType::Valid;
    }

    private function setAttribute(
        ?string $id = null,
        ?PlayerName $name = null,
        ?PlayerNumber $number = null,
        ?int $season = null,
        ?int $api_player_id = null,
        ?string $flash_id = null,
        ?string $flash_image_id = null): self
    {
        return new self(
            id: $id ?? $this->id,
            name: $name ?? $this->name,
            number: $number ?? $this->number,
            season: $season ?? $this->season,
            api_player_id: $api_player_id ?? $this->api_player_id,
            flash_id: $flash_id ?? $this->flash_id,
            flash_image_id: $flash_image_id ?? $this->flash_image_id,
        );
    }
}