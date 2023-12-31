<?php declare(strict_types=1);

namespace App\UseCases\Player\Util;

use GuzzleHttp\Client;


final readonly class SofaScore
{
    private const CHELSEA_ID = 38;

    private function __construct(private string $url, private array $query)
    {
        //
    }

    public function fetch()
    {
        $client = new Client();

        $response = $client->request('GET', $this->url, [
            'query' => $this->query,
            'delay' => 500,
            'headers' => [
                'X-RapidAPI-Host' => config('sofa-score.api-host'),
                'X-RapidAPI-Key'  => config('sofa-score.api-key')
            ],
        ]);

        return $response->getBody()->getContents();
    }

    //769333 Nkunku
    
    public static function playerPhoto(int $playerId): self
    {
        return new self(
            url: 'https://sofascores.p.rapidapi.com/v1/players/photo',
            query: [
                'player_id' => (string) $playerId
            ]
        );
    }

    public static function playersOfTeam(): self
    {
        return new self(
            url: 'https://sofascores.p.rapidapi.com/v1/teams/players',
            query: [
                'team_id' => (string) self::CHELSEA_ID
            ]
        );
    }
}