<?php declare(strict_types=1);

namespace App\UseCases\Admin;

use Illuminate\Support\Collection;

use App\UseCases\Admin\Data\FlashLiveSports\PlayerData;
use App\UseCases\Admin\Data\FlashLiveSports\PlayersData;
use App\UseCases\Admin\Data\FlashLiveSports\TeamSquad;


interface FlashLiveSportsRepositoryInterface
{
    public function fetchTeamSquad(): TeamSquad;
    public function fetchPlayer(string $flashLiveSportsId): PlayerData;
    public function searchPlayer(Collection $playerInfo): PlayersData;
    public function fetchPlayerImage(string $flash_live_sports_image_id): string;
}