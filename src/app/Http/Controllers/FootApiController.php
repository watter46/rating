<?php declare(strict_types=1);

namespace App\Http\Controllers;

use App\UseCases\Player\Util\FootApiFetcher;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;


/**
 * Playerのイメージ画像を取得できるAPI
 * 
 * https://rapidapi.com/fluis.lacasse/api/footapi7
 * 
 * rate: 50req/day 4req/sec
 */
class FootApiController extends Controller
{
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

            $image = FootApiFetcher::playerImage($playerId)->fetch();

            File::put($fileName, $image);
        }
    }
}
