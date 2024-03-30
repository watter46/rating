<?php declare(strict_types=1);

namespace App\UseCases\User\Fixture\Format\Fixture;

use Illuminate\Support\Collection;

use App\Http\Controllers\Util\TeamImageFile;


readonly class Teams
{
    public function __construct(private TeamImageFile $teamImage)
    {
        //   
    }
    
    // {
    //     +"home": {
    //         +"id": 49
    //         +"name": "Chelsea"
    //         +"logo": "https://media.api-sports.io/football/teams/49.png"
    //         +"winner": null
    //     }
    //     +"away": {
    //         +"id": 40
    //         +"name": "Liverpool"
    //         +"logo": "https://media.api-sports.io/football/teams/40.png"
    //         +"winner": null
    //     }
    // }
    public function build($data): Collection
    {
        return collect($data)
            ->map(function ($team) {
                return [
                    'id'   => $team->id,
                    'name' => $team->name,
                    'img'  => $this->teamImage->generatePath($team->id),
                    'winner' => $team->winner
                ];
            });
    }
}