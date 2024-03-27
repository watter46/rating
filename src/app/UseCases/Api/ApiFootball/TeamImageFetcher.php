<?php declare(strict_types=1);

namespace App\UseCases\Api\ApiFootball;

use Exception;
use Illuminate\Support\Facades\Http;

use App\Http\Controllers\Util\TeamImageFile;


readonly class TeamImageFetcher
{
    public function __construct(private TeamImageFile $file)
    {
        //
    }

    public function fetch(int $teamId): string
    {
        try {
            $response = Http::withHeaders([
                'X-RapidAPI-Host' => config('api-football.api-host'),
                'X-RapidAPI-Key'  => config('api-football.api-key')
            ])
            ->retry(1, 500)
            ->get("https://media-4.api-sports.io/football/teams/$teamId.png");

            return $response->throw()->body();

        } catch (Exception $e) {
            throw $e;
        }
    }
}