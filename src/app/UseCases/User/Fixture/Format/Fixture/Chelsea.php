<?php declare(strict_types=1);

namespace App\UseCases\User\Fixture\Format\Fixture;

use Illuminate\Support\Collection;


readonly class Chelsea
{
    private const CHELSEA_TEAM_ID = 49;
    
    public function filter($data): Collection
    {
        $chelsea = collect($data)
            ->sole(function ($teams) {
                return $teams->team->id === self::CHELSEA_TEAM_ID;
            });

        return collect($chelsea);
    }

    // [
    //     0 => {#1993
    //       +"team": {#2804
    //         +"id": 49
    //         +"name": "Chelsea"
    //         +"logo": "https://media.api-sports.io/football/teams/49.png"
    //         +"colors": {#2803
    //           +"player": {#2801
    //             +"primary": "1532c1"
    //             +"number": "ffffff"
    //             +"border": "1532c1"
    //           }
    //           +"goalkeeper": {#2802
    //             +"primary": "e3e3e3"
    //             +"number": "000000"
    //             +"border": "e3e3e3"
    //           }
    //         }
    //       }
    //       +"coach": {
    //         +"id": 13
    //         +"name": "M. Pochettino"
    //         +"photo": "https://media.api-sports.io/football/coachs/13.png"
    //       }
    //       +"formation": "3-4-2-1"
    //       +"startXI": array:11 [
    //         0 => {
    //           +"player": {
    //             +"id": 18959
    //             +"name": "Robert Sánchez"
    //             +"number": 31
    //             +"pos": "G"
    //             +"grid": "1:1"
    //           }
    //         }
    //         ~~~~~~~~~~~~~~~~
    //         10
    //       ]
    //       +"substitutes": array:9 [
    //         0 => {#2030
    //           +"player": {#2027
    //             +"id": 161907
    //             +"name": "M. Gusto"
    //             +"number": 27
    //             +"pos": "D"
    //             +"grid": null
    //           }
    //         }
    //         ~~~~~~~~~~~~
    //       ]
    //     }
    //     1 => {#2052
          
    //     }
    // ]
}