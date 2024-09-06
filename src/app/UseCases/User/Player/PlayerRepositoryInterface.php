<?php declare(strict_types=1);

namespace App\UseCases\User\Player;

use App\UseCases\User\Domain\Player\Player;
use App\UseCases\User\Domain\Player\PlayerId;


interface PlayerRepositoryInterface
{
    public function find(PlayerId $playerId): Player;
    public function save(Player $player);
}