<?php declare(strict_types=1);

namespace App\UseCases\Player\Util;

use App\UseCases\Util\Season;
use Exception;
use GuzzleHttp\Client;


final readonly class ApiFootball
{
    private const CHELSEA_TEAM_ID = 49;

    private function __construct(private readonly string $url, private readonly array $query)
    {
        //
    }

    public function fetch(): string
    {        
        try {
            $client = new Client();

            $response = $client->request('GET', $this->url, [
                'query' => $this->query,
                'delay' => 500,
                'headers' => [
                    'X-RapidAPI-Host' => config('api-football.api-host'),
                    'X-RapidAPI-Key'  => config('api-football.api-key')
                ],
            ]);

            return $response->getBody()->getContents();

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
                'delay' => 500,
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
        $season = new Season();
        
        return new self(
            url:  'https://api-football-v1.p.rapidapi.com/v3/fixtures',
            query: [
                'season' => $season->current(),
                'team'   => self::CHELSEA_TEAM_ID
            ]
        );
    }

    public static function fixture(int $fixtureId): self
    {
        return new self(
            url:  'https://api-football-v1.p.rapidapi.com/v3/fixtures',
            query: [
                'id' => $fixtureId
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