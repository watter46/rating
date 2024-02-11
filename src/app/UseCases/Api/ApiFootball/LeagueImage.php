<?php declare(strict_types=1);

namespace App\UseCases\Api\ApiFootball;

use Exception;
use GuzzleHttp\Client;

use App\Http\Controllers\Util\LeagueImageFile;


readonly class LeagueImage
{
    public function __construct(private LeagueImageFile $file)
    {
        //
    }

    public function register(int $leagueId): void
    {
        try {
            $client = new Client();

            $response = $client->request('GET', "https://media-4.api-sports.io/football/leagues/$leagueId.png", [
                'delay' => 500,
                'headers' => [
                    'X-RapidAPI-Host' => config('api-football.api-host'),
                    'X-RapidAPI-Key'  => config('api-football.api-key')
                ]
            ]);

            $leagueImage = $response->getBody()->getContents();

            $this->file->write($leagueId, $leagueImage);

        } catch (Exception $e) {
            throw $e;
        }
    }
}