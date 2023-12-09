<?php declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\File;


class FootApiController extends Controller
{
    public function index()
    {
        $data = File::get(app_path('Template/image'));

        $image = base64_encode($data);

        $data = [
            'image' => $image
        ];
        
        return view('players', $data);
    }

    public function show()
    {
        $this->fetchPlayerImages();
        
        $json = File::get(app_path('Template/players.json'));

        $players = json_decode($json);
        
        $data = [
            'image' => $players
        ];
        
        return view('players', $data);
    }

    public function fetchPlayerImages()
    {
        $json = File::get(app_path('Template/players.json'));

        $players = json_decode($json);

        $playerList = collect($players->players)
            ->map(function ($detail) {
                return collect([
                    'id'   => $detail->player->id,
                    'name' => Str::studly($detail->player->name)
                ]);
            });
    
        $path = public_path('images');

        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }

        $until = $playerList->take(13);

        dd($until);
                
        foreach($playerList as $player) {
            $fileName = $path.'/'.$player->get('name');

            $playerId = (string) $player->get('id');
            
            if (File::exists($fileName)) {
                continue;
            }

            $client = new Client();
                        
            $response = $client->request('GET', "https://footapi7.p.rapidapi.com/api/player/$playerId/image", [
                'headers' => [
                    'X-RapidAPI-Host' => 'footapi7.p.rapidapi.com',
                    'X-RapidAPI-Key'  => 'bfb866136fmshd1efe44101227e4p1a798cjsn7558902ddea2',
                ]
            ]);

            File::put($fileName, $response->getBody()->getContents());
        }
    }

    // TeamNearMatches
    
    public function players()
    {   
        $json = File::get(app_path('Template/players.json'));

        $players = json_decode($json)->players;

        $playerList = collect($players)
            ->map(function ($detail) {
                $name = Str::studly($detail->player->name);
                
                $path = public_path('images').'/'.$name;

                $image = File::exists($path) ? base64_encode(File::get($path)) : '';

                return (object)[
                    'id'     => $detail->player->id,
                    'name'   => $detail->player->shortName,
                    'number' => $detail->player->shirtNumber,
                    'img'    => $image
                ];
            });

        $data = ['data' => $playerList];

        return view('players', $data);
    }
    
    public function image()
    {
        // Chelsea team id: 38
        
        $client = new Client();

        $response = $client->request('GET', 'https://footapi7.p.rapidapi.com/api/player/904827/image', [
            'headers' => [
                'X-RapidAPI-Host' => 'footapi7.p.rapidapi.com',
                'X-RapidAPI-Key'  => 'bfb866136fmshd1efe44101227e4p1a798cjsn7558902ddea2',
            ],
        ]);
        
        // JSONデータをファイルに書き込む
        $path = app_path('Template/image');

        File::put($path, $response->getBody());
    }
}
