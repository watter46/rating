<?php declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\EvaluatePlayerRequest;
use App\Http\Resources\PlayerResource;
use Exception;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

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

            // dd($this->getPlayerImage());

            $json = File::get(app_path('Template/players.json'));

            $players = json_decode($json)->players;

            $players = collect($players)
                ->map(function ($detail) {
                    $name = Str::studly($detail->player->name);
                    
                    $path = public_path('images').'/'.$name;

                    $image = File::exists($path) ? base64_encode(File::get($path)) : '';

                    return (object)[
                        'id'       => $detail->player->id,
                        'name'     => $detail->player->shortName,
                        'number'   => $detail->player->shirtNumber,
                        'position' => PositionType::from($detail->player->position)->name,
                        'img'      => $image
                    ];
                });

            $players = collect([
                'FW' => [
                    $players[0],
                    $players[1],
                    $players[2],
                ],
                'MID' => [
                    'line' => [[
                        $players[3],
                        $players[4],
                        $players[5]
                    ]]
                ],
                'DF' => [
                    $players[6],
                    $players[7],
                    $players[8],
                    $players[9],
                ],
                'GK' => [
                    $players[10]
                ]
            ]);

            return view('players', compact('players'));

        } catch (Exception $e) {

        }
    }

    public function getPlayerImage($name = 'BenoîtBadiashile')
    {
        $json = File::get(public_path("images/$name"));

        dd(base64_encode($json));
    }
}