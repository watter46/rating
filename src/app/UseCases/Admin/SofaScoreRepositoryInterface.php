<?php declare(strict_types=1);

namespace App\UseCases\Admin;

use App\UseCases\Admin\Player\PlayersOfTeamData\PlayersOfTeamData;
use Illuminate\Support\Collection;

interface SofaScoreRepositoryInterface
{
    public function fetchPlayerByName(string $playerName): Collection;
    public function fetchPlayersOfTeam(): PlayersOfTeamData;
    public function fetchPlayerImage(int $playerId): string;
}