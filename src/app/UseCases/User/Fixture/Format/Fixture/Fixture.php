<?php declare(strict_types=1);

namespace App\UseCases\User\Fixture\Format\Fixture;

use Illuminate\Support\Collection;


readonly class Fixture
{
    private const END_STATUS = 'Match Finished';

    // {
    //     +"id": 1035045
    //     +"referee": "A. Taylor"
    //     +"timezone": "UTC"
    //     +"date": "2023-08-13T15:30:00+00:00"
    //     +"timestamp": 1691940600
    //     +"periods": {#2838
    //         +"first": 1691940600
    //         +"second": 1691944200
    //     }
    //     +"venue": {#2871
    //         +"id": 519
    //         +"name": "Stamford Bridge"
    //         +"city": "London"
    //     }
    //     +"status": {#2912
    //         +"long": "Match Finished"
    //         +"short": "FT"
    //         +"elapsed": 90
    //     }
    // }
    public function isFinished($data): bool
    {
        return $data->status->long === self::END_STATUS;
    }
    
    public function build($data): Collection
    {
        return collect([
            'id'             => $data->id,
            'first_half_at'  => $this->date($data->periods->first),
            'second_half_at' => $this->date($data->periods->second),
            'is_end'         => $data->status->long === self::END_STATUS
        ]);
    }

    private function date($timestamp): string
    {
        return date('Y-m-d H:i', $timestamp);
    }
}