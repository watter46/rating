<?php declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\EvaluatePlayerRequest;
use App\Http\Resources\PlayerResource;
use Exception;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use App\Http\Controllers\PositionType;
use GuzzleHttp\Client;


final class PlayerController extends Controller
{
    public function index()
    {
        try {
            $players = collect([
                'df'  => [2, 3, 4, 5],
                'mid' => [6, 7, 8, 9],
                'fw'  => [10, 11],
            ])->reverse();

            $path = app_path('Template/squads');

            $fileName = $path.'/'.now()->year.'_post_summer.json';

            $json = File::get($fileName);

            $players = json_decode($json)->response[0]->players;

            // $json = File::get(app_path('Template/players.json'));

            // $players = json_decode($json)->players;

            $statistics = $this->startingXI();

            $filtered = collect($players)->whereIn('id', $statistics->pluck('player.id')->toArray());

            // $filteredNotIn = (object) collect($statistics)->whereNotIn('name', collect($players)->pluck('name')->toArray());
            
            // statisticsには未登録の選手も取得される
            // 20選手取得される

            // dd($filtered);

            // PlayerIdで統一する

            $players = $filtered
                ->map(function ($player) use ($statistics) {
                    $path = public_path('images').'/'.now()->year.'_S_'.$player->id;

                    $image = File::exists($path) ? base64_encode(File::get($path)) : '';

                    // アルファベット + . の場合と普通の表記で場合分けする

                    // dd(PositionType::from($player->player->position)->name);
                    // $statistics->sole(fn ($statistic) => $statistic['id'] === $player->number);
                    
                    // return (object)[
                    //     'id'       => $player->id,
                    //     'name'     => $player->name,
                    //     'number'   => $player->number,
                    //     'position' => PositionType::from($player->position)->name,
                    //     'rating'   => $statistics->sole(fn ($statistic) => $statistic['id'] === $player->id)['rating'],
                    //     'img'      => $image
                    // ];
                    
                    return (object)[
                        'id'       => $player->id,
                        'name'     => $player->name,
                        'number'   => $player->number,
                        'position' => PositionType::from($player->position)->name,
                        'rating'   => $statistics->sole(fn ($statistic) => $statistic->player->id === $player->id)->statistics[0]->games->rating,
                        'img'      => $image
                    ];
                });
                
            $players = $players->values();
            
            $players = collect([
                'FW' => collect([
                    $players[0],
                    $players[1],
                    $players[2],
                ]),
                'MID' => collect([
                    'line' => [[
                        $players[3],
                        $players[4],
                        $players[5]
                    ]]
                ]),
                'DF' => collect([
                    $players[6],
                    $players[7],
                    $players[8],
                    $players[9],
                ]),
                'GK' => collect([
                    $players[10]
                ])
            ]);

            return view('players', compact('players'));

        } catch (Exception $e) {

        }
    }

    public function fixtures()
    {
        $path = app_path('Template/fixtures');
        
        $fileName = $path.'/'.now()->year.'_fixtures'.'.json';

        $json = File::get($fileName);

        $fixtures = json_decode($json)->response;
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

    public function statistic(int $id = 1035327)
    {
        $statisticsPath = app_path('Template/statistics');

        $fileName = $statisticsPath.'/'.$id.'_player_statistic.json';

        $json = File::get($fileName);

        $statistic = json_decode($json)->response;

        $ChelseaStatistic = collect($statistic)->filter(function ($team) {
            return $team->team->id === 49;
        })->sole();

        $ratings = collect($ChelseaStatistic->players)
            ->map(function ($player) {
                return [
                    'id'     => $player->player->id,
                    'name'   => $player->player->name,
                    'rating' => $player->statistics[0]->games->rating
                ];
            });
        
        return $ratings;
    }

    public function startingXI(int $id = 1035327)
    {
        $statisticsPath = app_path('Template/statistics');

        $fileName = $statisticsPath.'/'.$id.'_player_statistic.json';

        $json = File::get($fileName);

        $statistic = json_decode($json)->response;

        $ChelseaStatistic = collect($statistic)->filter(function ($team) {
            return $team->team->id === 49;
        })->sole();

        $startingPlayers = collect($ChelseaStatistic->players)
            ->filter(fn ($player) => !$player->statistics[0]->games->substitute);
        
        return $startingPlayers;
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

    public function squads()
    {
        $path = app_path('Template/squads');

        $fileName = $path.'/'.now()->year.'_post_summer.json';

        $json = File::get($fileName);

        $squads = json_decode($json)->response;

        dd($squads);
    }
}