<?php declare(strict_types=1);

namespace App\Http\Controllers;

use Exception;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\File;

use App\UseCases\Player\GetLineupUseCase;


final class PlayerController extends Controller
{
    public function index(GetLineupUseCase $getLineup)
    {
        try {
            $fixtureId = 1035327;

            return view('players', $getLineup->execute($fixtureId));

        } catch (Exception $e) {

        }
    }

    public function fetchPlayerStatistics()
    {
        $path = app_path('Template/fixtures');
        
        $fileName = $path.'/'.now()->year.'_fixtures'.'.json';

        $json = File::get($fileName);

        $fixtures = json_decode($json)->response;
    }

    public function fetchFixtureCurrentRound()
    {
        $client = new Client();

        $response = $client->request('GET', 'https://api-football-v1.p.rapidapi.com/v3/fixtures/rounds', [
            'query' => [
                'season'  => now()->year,
                'league'  => 39,
                'current' => 'true'
            ],
            'headers' => [
                'X-RapidAPI-Host' => 'api-football-v1.p.rapidapi.com',
                'X-RapidAPI-Key' => 'bfb866136fmshd1efe44101227e4p1a798cjsn7558902ddea2',
            ],
        ]);

        dd($response->getBody());
    }

    public function fetchSeasonFixtures()
    {        
        $path = app_path('Template/fixtures');

        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }        
        
        $fileName = $path.'/'.now()->year.'_fixtures'.'.json';
        
        $client = new Client();

        $response = $client->request('GET', 'https://api-football-v1.p.rapidapi.com/v3/fixtures', [
            'query' => [
                'season' => now()->year,
                'team'   => 49
            ],
            'headers' => [
                'X-RapidAPI-Host' => 'api-football-v1.p.rapidapi.com',
                'X-RapidAPI-Key' => 'bfb866136fmshd1efe44101227e4p1a798cjsn7558902ddea2',
            ],
        ]);

        File::put($fileName, $response->getBody());
    }

    public function fetchStatistic(int $id = 1035327)
    {
        // vs Everton fixtureId: 1035327

        $statisticsPath = app_path('Template/statistics');

        if (!file_exists($statisticsPath)) {
            mkdir($statisticsPath, 0777, true);
        }

        $fileName = $statisticsPath.'/'.$id.'_player_statistic.json';
        
        $client = new Client();

        $response = $client->request('GET', 'https://api-football-v1.p.rapidapi.com/v3/fixtures/players', [
            'query' => [
                'fixture' => 1035327
            ],
            'headers' => [
                'X-RapidAPI-Host' => 'api-football-v1.p.rapidapi.com',
                'X-RapidAPI-Key' => 'bfb866136fmshd1efe44101227e4p1a798cjsn7558902ddea2',
            ],
        ]);

        File::put($fileName, $response->getBody());
    }

    public function fetchStartingXI(int $id = 1035327)
    {
        $client = new Client();

        $response = $client->request('GET', 'https://api-football-v1.p.rapidapi.com/v3/fixtures/lineups', [
            'query' => [
                'fixture' => 1035327,
                'team'    => 49
            ],
            'headers' => [
                'X-RapidAPI-Host' => 'api-football-v1.p.rapidapi.com',
                'X-RapidAPI-Key' => 'bfb866136fmshd1efe44101227e4p1a798cjsn7558902ddea2',
            ],
        ]);

        // dd($response->getBody()->getContents());
        
        $path = app_path('Template/startingXI');

        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }

        $fileName = $path.'/'.$id.'_starting_xi.json';

        File::put($fileName, $response->getBody()->getContents());

        // $json = File::get($fileName);

        // $statistic = json_decode($json)->response;

        // $ChelseaStatistic = collect($statistic)->filter(function ($team) {
        //     return $team->team->id === 49;
        // })->sole();

        // $startingPlayers = collect($ChelseaStatistic->players)
        //     ->filter(fn ($player) => !$player->statistics[0]->games->substitute);
        
        // return $startingPlayers;
    }

    public function fetchSquads()
    {
        $client = new Client();

        $response = $client->request('GET', 'https://api-football-v1.p.rapidapi.com/v3/players/squads', [
            'query' => [
                'team' => '49'
            ],
            'headers' => [
                'X-RapidAPI-Host' => 'api-football-v1.p.rapidapi.com',
                'X-RapidAPI-Key' => 'bfb866136fmshd1efe44101227e4p1a798cjsn7558902ddea2',
            ],
        ]);

        // JSONデータをファイルに書き込む
        // 夏移籍後のデータ？summer winte
        $path = app_path('Template/squads');

        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }

        $fileName = $path.'/'.now()->year.'_post_summer.json';
        
        File::put($fileName, $response->getBody());
    }
}