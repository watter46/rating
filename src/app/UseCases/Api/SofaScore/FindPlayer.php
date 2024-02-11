<?php declare(strict_types=1);

namespace App\UseCases\Api\SofaScore;

use Illuminate\Support\Collection;
use GuzzleHttp\Client;
use Exception;


readonly class FindPlayer
{
    public function __construct()
    {
        //
    }

    public function fetch(string $name): Collection
    {
        try {            
            $client = new Client();

            $response = $client->request('GET', 'https://sofascores.p.rapidapi.com/v1/search/multi', [
                'query' => [
                    'query' => $name,
                    'group' => 'players'
                ],
                'delay' => 500,
                'headers' => [
                    'X-RapidAPI-Host' => config('sofa-score.api-host'),
                    'X-RapidAPI-Key'  => config('sofa-score.api-key')
                ]
            ]);

            $json = $response->getBody()->getContents();

            return $this->parse($json);

          } catch (Exception $e) {
            throw $e;
        }
    }
    
    private function parse(string $json): Collection
    {
        $decoded = json_decode($json)->data;

        return collect($decoded);
    }
}