<?php declare(strict_types=1);

namespace App\UseCases\Player\Util;

use Exception;
use GuzzleHttp\Client;


final readonly class ApiFootballFetcher
{
    private const CHELSEA_TEAM_ID = 49;

    private function __construct(private readonly string $url, private readonly array $query)
    {
        //
    }

    public function fetch(): array
    {        
        try {
            $client = new Client();

            $response = $client->request('GET', $this->url, [
                'query' => $this->query,
                'headers' => [
                    'X-RapidAPI-Host' => config('api-football.api-host'),
                    'X-RapidAPI-Key'  => config('api-football.api-key')
                ],
            ]);

            $json = json_decode($response->getBody()->getContents());

            if (!($json->response)) {                
                throw new Exception('API-FOOTBALL Error: '.$response->errors->fixture);
            }
                        
            return $json->response;

        } catch (Exception $e) {
            dd($e);
        }
    }

    public function fetchImage()
    {
        try {
            $client = new Client();

            $response = $client->request('GET', $this->url, [
                'query' => $this->query,
                'headers' => [
                    'X-RapidAPI-Host' => config('api-football.api-host'),
                    'X-RapidAPI-Key'  => config('api-football.api-key')
                ],
            ]);

            $image = $response->getBody()->getContents();

            if (!$image) {                
                throw new Exception('API-FOOTBALL Error: TeamId is invalid.');
            }
                        
            return $image;

        } catch (Exception $e) {
            dd($e);
        }
    }

    public static function fixtures(): self
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

    public static function squads(): self
    {
        return new self(
            url:  'https://api-football-v1.p.rapidapi.com/v3/players/squads',
            query: [
                'team' => self::CHELSEA_TEAM_ID
            ]
        );
    }

    public static function teamImage(int $teamId): self
    {
        return new self(
            url: "https://media-4.api-sports.io/football/teams/$teamId.png",
            query: []
        );
    }

    public static function leagueImage(int $leagueId): self
    {
        return new self(
            url: "https://media-4.api-sports.io/football/leagues/$leagueId.png",
            query: []
        );
    }
}