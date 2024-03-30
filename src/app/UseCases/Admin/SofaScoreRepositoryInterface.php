<?php declare(strict_types=1);

namespace App\UseCases\Admin;

use Illuminate\Support\Collection;

interface SofaScoreRepositoryInterface
{
    public function fetchPlayerByName(string $playerName): Collection;
    public function fetchPlayersOfTeam(): Collection;
    public function fetchPlayerImage(int $playerId): string;
}