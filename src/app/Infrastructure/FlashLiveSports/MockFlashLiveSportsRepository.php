<?php declare(strict_types=1);

namespace App\Infrastructure\FlashLiveSports;

use Exception;
use Illuminate\Support\Facades\Http;

use App\Http\Controllers\Util\Api\FlashLiveSports\PlayerFile;
use App\Http\Controllers\Util\Api\FlashLiveSports\PlayersFile;

use App\Http\Controllers\Util\TeamSquadFile;
use App\Http\Controllers\Util\TestPlayerImageFile;
use App\UseCases\Admin\Data\FlashLiveSports\PlayerData;
use App\UseCases\Admin\Data\FlashLiveSports\PlayersData;
use App\UseCases\Admin\Data\FlashLiveSports\TeamSquad;
use App\UseCases\Admin\Fixture\Accessors\Flash\FlashPlayer;
use App\UseCases\Admin\Fixture\Accessors\Flash\FlashPlayers;
use App\UseCases\Admin\Fixture\Accessors\Player;
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

    private function isSeed(): bool
    {
        return config('seeder.status');
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
        
        if ($this->isSeed()) {
            if ($this->teamSquadFile->exists()) {
                return TeamSquad::create($this->teamSquadFile->get());
            }

            dd('not exists');
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

    public function fetchPlayer(Player $player): FlashPlayer
    {
        $flashPlayerId = $player->getPlayerInfo()->getFlashPlayerId();

        if ($this->isTest()) {
            return FlashPlayer::create($this->playerFile->get($flashPlayerId));
        }

        if ($this->isSeed()) {
            if ($this->playerFile->exists($flashPlayerId)) {
                return FlashPlayer::create($this->playerFile->get($flashPlayerId));
            }

            dd('not exists');
        }

        if ($this->playerFile->exists($flashPlayerId)) {
            return FlashPlayer::create($this->playerFile->get($flashPlayerId));
        }
        
        $json = $this->httpClient('https://flashlive-sports.p.rapidapi.com/v1/players/data', [
                'player_id' => $flashPlayerId,
                'sport_id'  => config('flash-live-sports.sport-id'),
                'locale'    => config('flash-live-sports.locale')
            ]);
            
        $data = collect(json_decode($json)->DATA);

        $this->playerFile->write($flashPlayerId, $data);
        
        return FlashPlayer::create($data);
    }

    public function searchPlayer(Player $player): FlashPlayer
    {
        if ($this->isTest()) {
            return FlashPlayer::fromPlayers($this->playersFile->get($player->getPlayerId()));
        }

        if ($this->isSeed()) {
            if ($this->playersFile->exists($player->getPlayerId())) {
                return FlashPlayer::fromPlayers($this->playersFile->get($player->getPlayerId()));
            }

            dd('not exists');
        }

        if ($this->playersFile->exists($player->getPlayerId())) {
            return FlashPlayer::fromPlayers($this->playersFile->get($player->getPlayerId()));
        }
        
        $json = $this->httpClient('https://flashlive-sports.p.rapidapi.com/v1/search/multi-search', [
                'locale' => config('flash-live-sports.locale'),
                'query'  => $player->getName()
            ]);
            
        $data = collect(json_decode($json));

        $this->playersFile->write($player->getPlayerId(), $data);
        
        return FlashPlayer::fromPlayers($data);
    }

    public function fetchPlayerImage(Player $player): string
    {
        $playerInfo = $player->getPlayerInfo();

        if ($this->isTest()) {
            return $this->testPlayerImageFile->get();
        }

        return $this->httpClient('https://flashlive-sports.p.rapidapi.com/v1/images/data', [
                'image_id'=> $playerInfo->getImageId()
            ]);
    }
}