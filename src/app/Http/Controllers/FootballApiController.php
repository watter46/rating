<?php declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use GuzzleHttp\Client;


class FootballApiController extends Controller
{
    public function index()
    {
        $json = File::get(app_path('Template/static.json'));

        $data = [
            'data' => json_decode($json)->response[0]->players
        ];

        return view('api', $data);
    }

    public function static()
    {
        $fixture = File::get(app_path('Template/league.json'));

        $data = json_decode($fixture)->response;
        
        $currentFixture = collect($data)
            ->filter(function ($data) {
                return $data->league->id === 39;
            })
            ->filter(function ($data) {
                return $data->fixture->status->long === 'Match Finished';
            })
            ->last();
        
        $currentFixtureId = $currentFixture->fixture->id;

        $client = new Client();

        $response = $client->request('GET', 'https://api-football-v1.p.rapidapi.com/v3/fixtures/players', [
            'query' => [
                'fixture' => "$currentFixtureId",
                'team' => '49'
            ],
            'headers' => [
                'X-RapidAPI-Host' => 'api-football-v1.p.rapidapi.com',
                'X-RapidAPI-Key' => 'bfb866136fmshd1efe44101227e4p1a798cjsn7558902ddea2',
            ],
        ]);

        // JSONデータをファイルに書き込む
        $path = app_path('Template/static.json');
        
        File::put($path, $response->getBody()->getContents());
    }

    public function league()
    {
        // Premier League id: 39
        // Chelsea team-id: 49
        
        $client = new Client();

        $response = $client->request('GET', 'https://api-football-v1.p.rapidapi.com/v3/fixtures', [
            'query' => [
                'team'    => '49',
                'season'  => '2023'
            ],
            'headers' => [
                'X-RapidAPI-Host' => 'api-football-v1.p.rapidapi.com',
                'X-RapidAPI-Key' => 'bfb866136fmshd1efe44101227e4p1a798cjsn7558902ddea2',
            ],
        ]);

        // JSONデータをファイルに書き込む
        $path = app_path('Template/league.json');
        
        File::put($path, $response->getBody()->getContents());
    }

    public function current()
    {
        $client = new Client();

        $response = $client->request('GET', 'https://api-football-v1.p.rapidapi.com/v3/fixtures/rounds', [
            'query' => [
                'league'  => '39',
                'season'  => '2023',
                'current' => 'true'
            ],
            'headers' => [
                'X-RapidAPI-Host' => 'api-football-v1.p.rapidapi.com',
                'X-RapidAPI-Key' => 'bfb866136fmshd1efe44101227e4p1a798cjsn7558902ddea2',
            ],
        ]);

        // JSONデータをファイルに書き込む
        $path = app_path('Template/currentRound.json');
        
        File::put($path, $response->getBody()->getContents());
    }
}
