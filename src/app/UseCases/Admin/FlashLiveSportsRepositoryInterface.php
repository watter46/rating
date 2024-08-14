<?php declare(strict_types=1);

namespace App\UseCases\Admin;

use App\UseCases\Admin\Data\FlashLiveSports\PlayerData;
use App\UseCases\Admin\Data\FlashLiveSports\PlayersData;
use App\UseCases\Admin\Data\FlashLiveSports\TeamSquad;
use App\UseCases\Admin\Fixture\Accessors\Player;


interface FlashLiveSportsRepositoryInterface
{
    public function fetchTeamSquad(): TeamSquad;
    public function fetchPlayer(Player $player): PlayerData;
    public function searchPlayer(Player $player): PlayersData;
    public function fetchPlayerImage(Player $player): string;
}