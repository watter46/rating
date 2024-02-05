<?php declare(strict_types=1);

namespace App\UseCases\Fixture\Format\Fixture;

use Illuminate\Support\Collection;

use App\Http\Controllers\Util\LeagueImageFile;


readonly class League
{
    public function __construct(private LeagueImageFile $leagueImage)
    {
        //
    }

    // {
    //     +"id": 39
    //     +"name": "Premier League"
    //     +"country": "England"
    //     +"logo": "https://media.api-sports.io/football/leagues/39.png"
    //     +"flag": "https://media.api-sports.io/flags/gb.svg"
    //     +"season": 2023
    //     +"round": "Regular Season - 1"
    // }
    public function build($data): Collection
    {
        return collect([
            'id'     => $data->id,
            'name'   => $data->name,
            'season' => $data->season,
            'round'  => $data->round,
            'img'    => $this->leagueImage->generatePath($data->id)
        ]);
    }
}