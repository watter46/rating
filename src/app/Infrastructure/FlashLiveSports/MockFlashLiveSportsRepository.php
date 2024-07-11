<?php declare(strict_types=1);

namespace App\Infrastructure\FlashLiveSports;

use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

use App\Http\Controllers\Util\Api\FlashLiveSports\PlayerFile;
use App\Http\Controllers\Util\Api\FlashLiveSports\PlayersFile;

use App\Http\Controllers\Util\TeamSquadFile;
use App\Http\Controllers\Util\TestPlayerImageFile;
use App\UseCases\Admin\Data\FlashLiveSports\PlayerData;
use App\UseCases\Admin\Data\FlashLiveSports\PlayersData;
use App\UseCases\Admin\Data\FlashLiveSports\TeamSquad;
use App\UseCases\Admin\FlashLiveSportsRepositoryInterface;


class MockFlashLiveSportsRepository implements FlashLiveSportsRepositoryInterface
{
    public function __construct(
        private TeamSquadFile $teamSquadFile,
        private PlayerFile $playerFile,
        private PlayersFile $playersFile,
        private TestPlayerImageFile $testPlayerImageFile)
    {
        //
    }

    private function isTest(): bool
    {
        return env('APP_ENV') === 'testing';
    }

    private function httpClient(string $url, ?array $queryParams = null): string
    {
        try {
            $response = Http::withHeaders([
                'X-RapidAPI-Host' => config('flash-live-sports.api-host'),
                'X-RapidAPI-Key'  => config('flash-live-sports.api-key')
            ])
            ->retry(1, 500)
            ->get($url, $queryParams);
    
            return $response->throw()->body();
        } catch (Exception $e) {
            dd($e);
        }
    }

    public function fetchTeamSquad(): TeamSquad
    {
        if ($this->isTest()) {
            return TeamSquad::create($this->teamSquadFile->get());
        }

        if ($this->teamSquadFile->exists()) {
            return TeamSquad::create($this->teamSquadFile->get());
        }
        
        $json = $this->httpClient('https://flashlive-sports.p.rapidapi.com/v1/teams/squad', [
                'sport_id' => config('flash-live-sports.sport-id'),
                'team_id'  => config('flash-live-sports.chelsea-id'),
                'locale'   => config('flash-live-sports.locale')
            ]);
            
        $data = collect(json_decode($json)->DATA);

        $this->teamSquadFile->write($data);
        
        return TeamSquad::create($data);
    }

    public function fetchPlayer(string $flashLiveSportsId): PlayerData
    {
        if ($this->isTest()) {
            return PlayerData::create($this->playerFile->get($flashLiveSportsId));
        }

        if ($this->playerFile->exists($flashLiveSportsId)) {
            return PlayerData::create($this->playerFile->get($flashLiveSportsId));
        }
        
        $json = $this->httpClient('https://flashlive-sports.p.rapidapi.com/v1/players/data', [
                'player_id' => $flashLiveSportsId,
                'sport_id'  => config('flash-live-sports.sport-id'),
                'locale'    => config('flash-live-sports.locale')
            ]);
            
        $data = collect(json_decode($json)->DATA);

        $this->playerFile->write($flashLiveSportsId, $data);
        
        return PlayerData::create($data);
    }

    public function searchPlayer(Collection $playerInfo): PlayersData
    {
        if ($this->isTest()) {
            return PlayersData::create($this->playersFile->get($playerInfo['api_football_id']));
        }

        if ($this->playersFile->exists($playerInfo['api_football_id'])) {
            return PlayersData::create($this->playersFile->get($playerInfo['api_football_id']));
        }

        $json = $this->httpClient('https://flashlive-sports.p.rapidapi.com/v1/search/multi-search', [
                'locale' => config('flash-live-sports.locale'),
                'query'  => $playerInfo['name']
            ]);
            
        $data = collect(json_decode($json));

        $this->playersFile->write($playerInfo['api_football_id'], $data);
        
        return PlayersData::create($data);
    }

    public function fetchPlayerImage(string $flash_live_sports_image_id): string
    {
        if ($this->isTest()) {
            return $this->testPlayerImageFile->get();
        }

        return $this->httpClient('https://flashlive-sports.p.rapidapi.com/v1/images/data', [
                'image_id'=> $flash_live_sports_image_id
            ]);
    }
}