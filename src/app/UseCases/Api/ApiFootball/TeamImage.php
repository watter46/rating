<?php declare(strict_types=1);

namespace App\UseCases\Api\ApiFootball;

use Exception;
use GuzzleHttp\Client;

use App\Http\Controllers\Util\TeamImageFile;


readonly class TeamImage
{
    public function __construct(private TeamImageFile $file)
    {
        //
    }

    public function register(int $teamId): string
    {
        try {            
            $client = new Client();

            $response = $client->request('GET', "https://media-4.api-sports.io/football/teams/$teamId.png", [
                'delay' => 500,
                'headers' => [
                    'X-RapidAPI-Host' => config('api-football.api-host'),
                    'X-RapidAPI-Key'  => config('api-football.api-key')
                ]
            ]);

            return $response->getBody()->getContents();

        } catch (Exception $e) {
            throw $e;
        }
    }
}