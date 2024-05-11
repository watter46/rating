<?php declare(strict_types=1);

namespace App\Infrastructure\SofaScore;

use App\Http\Controllers\Util\PlayerFile;
use App\Http\Controllers\Util\PlayerOfTeamFile;
use App\UseCases\Admin\Player\PlayerData\PlayerData;
use App\UseCases\Admin\Player\PlayersOfTeamData\PlayersOfTeamData;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

use App\UseCases\Admin\SofaScoreRepositoryInterface;


class SofaScoreRepository implements SofaScoreRepositoryInterface
{
    public function __construct(private PlayerFile $playerFile, private PlayerOfTeamFile $playerOfTeamFile)
    {
        
    }

    private function httpClient(string $url, ?array $queryParams = null): string
    {
        $response = Http::withHeaders([
            'X-RapidAPI-Host' => config('sofa-score.api-host'),
            'X-RapidAPI-Key'  => config('sofa-score.api-key')
        ])
        ->retry(1, 500)
        ->get($url, $queryParams);

        return $response->throw()->body();
    }

    public function fetchPlayer(array $player): PlayerData
    {
        $json = $this->httpClient('https://sofascores.p.rapidapi.com/v1/search/multi', [
            'query' => $player['name'],
            'group' => 'players'
        ]);

        $data = collect(json_decode($json)->data);

        $this->playerFile->write($player['id'], $data);
        
        return PlayerData::create($player['id'], $data);
    } 
    
    public function fetchPlayersOfTeam(): PlayersOfTeamData
    {
        $json = $this->httpClient('https://sofascores.p.rapidapi.com/v1/teams/players', [
            'team_id' => (string) config('sofa-score.chelsea-id')
        ]);
  
        $data = collect(json_decode($json)->data);

        $this->playerOfTeamFile->write($data);
        
        return PlayersOfTeamData::create($data);
    }

    public function fetchPlayerImage(int $playerId): string
    {
        return $this->httpClient('https://sofascores.p.rapidapi.com/v1/players/photo', [
            'player_id' => (string) $playerId
        ]);
    }
}