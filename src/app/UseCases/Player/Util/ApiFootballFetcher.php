<?php declare(strict_types=1);

namespace App\UseCases\Player\Util;

use GuzzleHttp\Client;

final readonly class ApiFootballFetcher
{
    const CHELSEA_TEAM_ID = 49;

    private function __construct(private readonly string $url, private readonly array $query)
    {
        //
    }

    public function fetch(): string
    {
        $client = new Client();

        $response = $client->request('GET', $this->url, [
            'query' => [
                $this->query
            ],
            'headers' => [
                'X-RapidAPI-Host' => config('api-football.api-host'),
                'X-RapidAPI-Key'  => config('api-football.api-key')
            ],
        ]);

        return $response->getBody()->getContents();
    }

    public static function seasonFixtures(): self
    {
        return new self(
            url:  'https://api-football-v1.p.rapidapi.com/v3/fixtures',
            query: [
                'season' => now()->year,
                'team'   => self::CHELSEA_TEAM_ID
            ]
        );
    }

    public static function statistic(int $fixtureId): self
    {
        return new self(
            url:  'https://api-football-v1.p.rapidapi.com/v3/fixtures/players',
            query: [
                'fixture' => $fixtureId
            ]
        );
    }

    public static function lineup(int $fixtureId): self
    {
        return new self(
            url:  'https://api-football-v1.p.rapidapi.com/v3/fixtures/lineups',
            query: [
                'fixture' => $fixtureId,
                'team'    => self::CHELSEA_TEAM_ID
            ]
        );
    }

    public static function allPlayer(): self
    {
        return new self(
            url:  'https://api-football-v1.p.rapidapi.com/v3/players/squads',
            query: [
                'team' => self::CHELSEA_TEAM_ID
            ]
        );
    }
}