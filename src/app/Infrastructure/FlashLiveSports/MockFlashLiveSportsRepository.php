<?php declare(strict_types=1);

namespace App\Infrastructure\FlashLiveSports;

use Exception;
use Illuminate\Support\Facades\Http;

use App\Http\Controllers\Util\Api\FlashLiveSports\PlayerFile;
use App\Http\Controllers\Util\Api\FlashLiveSports\PlayersFile;

use App\Http\Controllers\Util\TeamSquadFile;
use App\Http\Controllers\Util\TestPlayerImageFile;
use App\UseCases\Admin\Fixture\Accessors\Flash\FlashPlayer;
use App\UseCases\Admin\Fixture\Accessors\Flash\FlashSquad;
use App\UseCases\Admin\Fixture\Accessors\PlayerInfo;
use App\UseCases\Admin\Fixture\Accessors\PlayerInfos;
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

    public function fetchSquad(): PlayerInfos
    {
        if ($this->isTest()) {
            return PlayerInfos::fromFlashSquad(FlashSquad::create($this->teamSquadFile->get()));
        }
        
        if ($this->isSeed()) {
            if ($this->teamSquadFile->exists()) {
                return PlayerInfos::fromFlashSquad(FlashSquad::create($this->teamSquadFile->get()));
            }
        }

        if ($this->teamSquadFile->exists()) {
            return PlayerInfos::fromFlashSquad(FlashSquad::create($this->teamSquadFile->get()));
        }
        
        $json = $this->httpClient('https://flashlive-sports.p.rapidapi.com/v1/teams/squad', [
                'sport_id' => config('flash-live-sports.sport-id'),
                'team_id'  => config('flash-live-sports.chelsea-id'),
                'locale'   => config('flash-live-sports.locale')
            ]);
            
        $data = collect(json_decode($json)->DATA);

        $this->teamSquadFile->write($data);
        
        return PlayerInfos::fromFlashSquad(FlashSquad::create($data));
    }

    public function fetchPlayer(PlayerInfo $playerInfo): FlashPlayer
    {
        // if ($this->isTest()) {
        //     return FlashPlayer::fromPlayer($this->playerFile->get($player->getFlashId()));
        // }

        // if ($this->isSeed()) {
        //     if ($this->playerFile->exists($player->getFlashId())) {
        //         return FlashPlayer::fromPlayer($this->playerFile->get($player->getFlashId()));
        //     }

        //     dd('not exists');
        // }

        // if ($this->playerFile->exists($player->getFlashId())) {
        //     return FlashPlayer::fromPlayer($this->playerFile->get($player->getFlashId()));
        // }
        
        // $json = $this->httpClient('https://flashlive-sports.p.rapidapi.com/v1/players/data', [
        //         'player_id' => $player->getFlashId(),
        //         'sport_id'  => config('flash-live-sports.sport-id'),
        //         'locale'    => config('flash-live-sports.locale')
        //     ]);
            
        // $data = collect(json_decode($json)->DATA);

        // $this->playerFile->write($player->getFlashId(), $data);
        
        // return FlashPlayer::fromPlayer($data);
    }

    public function searchPlayer(PlayerInfo $playerInfo): FlashPlayer
    {
        if ($this->isTest()) {
            return FlashPlayer::fromPlayers($this->playersFile->get($playerInfo->getPlayerId()));
        }

        if ($this->isSeed()) {
            if ($this->playersFile->exists($playerInfo->getPlayerId())) {
                return FlashPlayer::fromPlayers($this->playersFile->get($playerInfo->getPlayerId()));
            }
        }

        if ($this->playersFile->exists($playerInfo->getPlayerId())) {
            return FlashPlayer::fromPlayers($this->playersFile->get($playerInfo->getPlayerId()));
        }
        
        $json = $this->httpClient('https://flashlive-sports.p.rapidapi.com/v1/search/multi-search', [
                'locale' => config('flash-live-sports.locale'),
                'query'  => $playerInfo->getName()
            ]);
            
        $data = collect(json_decode($json));

        $this->playersFile->write($playerInfo->getPlayerId(), $data);
        
        return FlashPlayer::fromPlayers($data);
    }

    public function fetchPlayerImage(PlayerInfo $playerInfo): string
    {
        if ($this->isTest()) {
            return $this->testPlayerImageFile->get();
        }

        return $this->httpClient('https://flashlive-sports.p.rapidapi.com/v1/images/data', [
                'image_id'=> $playerInfo->getImageId()
            ]);
    }
}