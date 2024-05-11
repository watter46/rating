<?php declare(strict_types=1);

namespace App\UseCases\Admin;

use Illuminate\Support\Collection;

use App\UseCases\Admin\Player\PlayersOfTeamData\PlayersOfTeamData;
use App\UseCases\Admin\Player\PlayerData\PlayerData;


interface SofaScoreRepositoryInterface
{    
    /**
     * SofaScoreからプレイヤーを取得する
     *
     * @param  array{ id: int, name: string } $player
     * @return PlayerData
     */
    public function fetchPlayer(array $player): PlayerData;
    public function fetchPlayersOfTeam(): PlayersOfTeamData;
    public function fetchPlayerImage(int $playerId): string;
}