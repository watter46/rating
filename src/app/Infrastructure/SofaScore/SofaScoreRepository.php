<?php declare(strict_types=1);

namespace App\Infrastructure\SofaScore;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

use App\UseCases\Admin\SofaScoreRepositoryInterface;


class SofaScoreRepository implements SofaScoreRepositoryInterface
{
    public function __construct()
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

    public function fetchPlayerByName(string $playerName): Collection
    {
        $json = $this->httpClient('https://sofascores.p.rapidapi.com/v1/search/multi', [
            'query' => $playerName,
            'group' => 'players'
        ]);

        return collect(json_decode($json)->data);
    }
    
    public function fetchPlayersOfTeam(): Collection
    {
        $json = $this->httpClient('https://sofascores.p.rapidapi.com/v1/teams/players', [
            'team_id' => (string) config('sofa-score.chelsea-id')
        ]);

        return collect(json_decode($json)->data);
    }

    public function fetchPlayerImage(int $playerId): string
    {
        return $this->httpClient('https://sofascores.p.rapidapi.com/v1/players/photo', [
            'player_id' => (string) $playerId
        ]);
    }
}