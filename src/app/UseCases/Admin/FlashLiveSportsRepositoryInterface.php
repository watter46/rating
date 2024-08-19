<?php declare(strict_types=1);

namespace App\UseCases\Admin;

use App\UseCases\Admin\Fixture\Accessors\Flash\FlashPlayer;
use App\UseCases\Admin\Fixture\Accessors\Flash\FlashSquad;
use App\UseCases\Admin\Fixture\Accessors\PlayerInfo;


interface FlashLiveSportsRepositoryInterface
{
    public function fetchSquad(): FlashSquad;
    public function fetchPlayer(PlayerInfo $playerInfo): FlashPlayer;
    public function searchPlayer(PlayerInfo $playerInfo): FlashPlayer;
    public function fetchPlayerImage(PlayerInfo $playerInfo): string;
}