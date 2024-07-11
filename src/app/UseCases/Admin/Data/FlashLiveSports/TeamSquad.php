<?php declare(strict_types=1);

namespace App\UseCases\Admin\Data\FlashLiveSports;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;

use App\Models\PlayerInfo;
use App\UseCases\Admin\Player\Processors\PlayerInfos\PlayerDataMatcher;


class TeamSquad
{
    private PlayerImageChecker $checker;
    
    private function __construct(private Collection $teamSquad)
    {
        $this->checker = new PlayerImageChecker($this);
    }
    
    public static function create(Collection $teamSquad): self
    {
        return (new self($teamSquad));
    }

    public function getByPlayerInfo(PlayerDataMatcher $matcher)
    {
        return $this->getPlayers()->first(fn ($player) => $matcher->match($player));
    }

    public function getPlayers(): Collection
    {
        return $this->teamSquad
            ->flatten(1)
            ->map(function ($group) {
                $swapFirstAndLastName = function ($name) {
                    $names = collect(Str::of($name)->explode(' '));
                                            
                    return $names->reverse()->implode(' ');
                };

                $pathToImageId = function (string $path) {
                    $png = Str::afterLast($path, '/');

                    return Str::beforeLast($png, '.png');
                };

                return collect($group->ITEMS)
                    ->map(function ($player) use ($swapFirstAndLastName, $pathToImageId) {                        
                        return [
                            'id' => $player->PLAYER_ID,
                            'name' => $swapFirstAndLastName($player->PLAYER_NAME),
                            'number' => $player->PLAYER_JERSEY_NUMBER ?? null,
                            'imageId' => $player->PLAYER_IMAGE_PATH
                                ? $pathToImageId($player->PLAYER_IMAGE_PATH)
                                : null
                        ];
                    });
            })
            ->flatten(1);
    }

    public function check()
    {
        return $this->checker->check();
    }

    public function getInvalidPlayerInfos()
    {
        return $this->checker->invalidPlayerInfos();
    }

    public function imagePaths(): Collection
    {
        $invalidPlayerInfos = $this->checker->invalidPlayerInfos();

        $invalidData = $this->teamSquad
            ->flatten(1)
            ->map(function ($group) {
                return collect($group->ITEMS)
                    ->map(fn($player) => [
                        'id' => $player->PLAYER_ID,
                        'path' => $player->PLAYER_IMAGE_PATH
                    ]);
            })
            ->flatten(1)
            ->whereIn('id', $invalidPlayerInfos->pluck('flash_live_sports_id')->toArray())
            ->keyBy('id');

        return $invalidPlayerInfos
            ->map(function (PlayerInfo $playerInfo) use ($invalidData) {
                $playerInfo->path = $invalidData->get($playerInfo->flash_live_sports_id)['path'];

                return $playerInfo;
            });
    }
}