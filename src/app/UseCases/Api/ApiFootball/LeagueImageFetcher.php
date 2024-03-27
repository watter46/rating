<?php declare(strict_types=1);

namespace App\UseCases\Api\ApiFootball;

use Exception;
use Illuminate\Support\Facades\Http;

use App\Http\Controllers\Util\LeagueImageFile;


readonly class LeagueImageFetcher
{
    public function __construct(private LeagueImageFile $file)
    {
        //
    }

    public function fetch(int $leagueId): string
    {
        try {
            $response = Http::withHeaders([
                'X-RapidAPI-Host' => config('api-football.api-host'),
                'X-RapidAPI-Key'  => config('api-football.api-key')
            ])
            ->retry(1, 500)
            ->get("https://media-4.api-sports.io/football/leagues/$leagueId.png");

            return $response->throw()->body();

        } catch (Exception $e) {
            throw $e;
        }
    }
}