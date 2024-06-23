<?php declare(strict_types=1);

namespace App\Infrastructure\SofaScore;

use App\Http\Controllers\Util\PlayerFile;
use App\Http\Controllers\Util\PlayerImageFile;
use App\Http\Controllers\Util\PlayerOfTeamFile;
use App\UseCases\Admin\Player\PlayerData\PlayerData;
use App\UseCases\Admin\Player\PlayersOfTeamData\PlayersOfTeamData;
use App\UseCases\Admin\SofaScoreRepositoryInterface;


class MockSofaScoreRepository implements SofaScoreRepositoryInterface
{
    public function __construct(
        private PlayerFile $playerFile,
        private PlayerOfTeamFile $playerOfTeamFile,
        private PlayerImageFile $playerImageFile)
    {
        
    }

    public function fetchPlayer(array $player): PlayerData
    {
        return PlayerData::create($player['id'], $this->playerFile->get($player['id']));
    }

    public function fetchPlayersOfTeam(): PlayersOfTeamData
    {
        return PlayersOfTeamData::create($this->playerOfTeamFile->get());
    }

    public function fetchPlayerImage(int $playerId): string
    {
        return $this->playerImageFile->get($playerId);
    }
}