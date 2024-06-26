<?php declare(strict_types=1);

namespace App\Infrastructure\SofaScore;

use Exception;
use Illuminate\Support\Facades\Http;

use App\Http\Controllers\Util\PlayerFile;
use App\Http\Controllers\Util\PlayerImageFile;
use App\Http\Controllers\Util\PlayerOfTeamFile;
use App\Http\Controllers\Util\TestPlayerImageFile;
use App\UseCases\Admin\Player\PlayerData\PlayerData;
use App\UseCases\Admin\Player\PlayersOfTeamData\PlayersOfTeamData;
use App\UseCases\Admin\SofaScoreRepositoryInterface;


class MockSofaScoreRepository implements SofaScoreRepositoryInterface
{
    public function __construct(
        private PlayerFile $playerFile,
        private PlayerOfTeamFile $playerOfTeamFile,
        private PlayerImageFile $playerImageFile,
        private TestPlayerImageFile $testPlayerImageFile)
    {
        
    }

    private function isTest(): bool
    {
        return env('APP_ENV') === 'testing';
    }

    private function httpClient(string $url, ?array $queryParams = null): string
    {
        try {
            $response = Http::withHeaders([
                'X-RapidAPI-Host' => config('sofa-score.api-host'),
                'X-RapidAPI-Key'  => config('sofa-score.api-key')
            ])
            ->retry(1, 500)
            ->get($url, $queryParams);
    
            return $response->throw()->body();
            
        } catch (Exception $e) {
            dd($e);
        }
    }
    
    public function fetchPlayer(array $player): PlayerData
    {
        if ($this->isTest()) {
            return PlayerData::create($player['id'], $this->playerFile->get($player['id']));
        }

        if ($this->playerFile->exists($player['id'])) {
            return PlayerData::create($player['id'], $this->playerFile->get($player['id']));
        }

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
        if ($this->isTest()) {
            return PlayersOfTeamData::create($this->playerOfTeamFile->get());
        }

        if ($this->playerOfTeamFile->exists()) {
            return PlayersOfTeamData::create($this->playerOfTeamFile->get());
        }

        $json = $this->httpClient('https://sofascores.p.rapidapi.com/v1/teams/players', [
                'team_id' => (string) config('sofa-score.chelsea-id')
            ]);
  
        $data = collect(json_decode($json)->data);

        $this->playerOfTeamFile->write($data);
        
        return PlayersOfTeamData::create($data);
    }

    public function fetchPlayerImage(int $playerId): string
    {
        if ($this->isTest()) {
            $file = new TestPlayerImageFile;

            return $file->get();
        }
        
        if ($this->playerImageFile->exists($playerId)) {
            return $this->playerImageFile->get($playerId);
        }

        return $this->httpClient('https://sofascores.p.rapidapi.com/v1/players/photo', [
                'player_id' => (string) $playerId
            ]);
    }
}