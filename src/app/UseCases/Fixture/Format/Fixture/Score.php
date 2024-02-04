<?php declare(strict_types=1);

namespace App\UseCases\Fixture\Format\Fixture;

use Illuminate\Support\Collection;


readonly class Score
{
    // {
    //     +"halftime": {
    //         +"home": 1
    //         +"away": 1
    //     }
    //     +"fulltime": {
    //         +"home": 1
    //         +"away": 1
    //     }
    //     +"extratime": {
    //         +"home": null
    //         +"away": null
    //     }
    //     +"penalty": {
    //         +"home": null
    //         +"away": null
    //     }
    // }
    public function build($data): Collection
    {
        return collect($data)->except('halftime');
    }
}