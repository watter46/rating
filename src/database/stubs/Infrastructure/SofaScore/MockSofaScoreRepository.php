<?php declare(strict_types=1);

namespace Database\Stubs\Infrastructure\SofaScore;

use App\Http\Controllers\Util\PlayerOfTeamFile;
use App\UseCases\Admin\Player\PlayersOfTeamData\PlayersOfTeamData;
use App\UseCases\Admin\SofaScoreRepositoryInterface;
use Illuminate\Support\Collection;

class MockSofaScoreRepository implements SofaScoreRepositoryInterface
{
    public function __construct(private PlayerOfTeamFile $playerOfTeamFile)
    {
        
    }
    
    public function fetchPlayerByName(string $playerName): Collection
    {
        $json = $this->httpClient('https://sofascores.p.rapidapi.com/v1/search/multi', [
            'query' => $playerName,
            'group' => 'players'
        ]);

        return collect(json_decode($json)->data);
    }

    public function fetchPlayersOfTeam(): PlayersOfTeamData
    {
        return PlayersOfTeamData::create($this->playerOfTeamFile->get());
    }

    public function fetchPlayerImage(int $playerId): string
    {
        return $this->httpClient('https://sofascores.p.rapidapi.com/v1/players/photo', [
            'player_id' => (string) $playerId
        ]);
    }
}