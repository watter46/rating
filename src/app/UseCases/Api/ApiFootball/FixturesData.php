<?php declare(strict_types=1);

namespace App\UseCases\Api\ApiFootball;

use Exception;
use GuzzleHttp\Client;
use Illuminate\Support\Collection;

use App\UseCases\Util\Season;
use App\Http\Controllers\Util\FixturesFile;


readonly class FixturesData
{
    private const CHELSEA_TEAM_ID = 49;

    public function __construct(private FixturesFile $file)
    {
        //
    }

    public function fetch(): Collection
    {
        try {            
            $client = new Client();

            $response = $client->request('GET', 'https://api-football-v1.p.rapidapi.com/v3/fixtures', [
                'query' => [
                    'season' => Season::current(),
                    'team'   => self::CHELSEA_TEAM_ID
                ],
                'delay' => 500,
                'headers' => [
                    'X-RapidAPI-Host' => config('api-football.api-host'),
                    'X-RapidAPI-Key'  => config('api-football.api-key')
                ]
            ]);

            $json = $response->getBody()->getContents();

            return $this->parse($json);

        } catch (Exception $e) {
            throw $e;
        }
    }

    public function getFile(): Collection
    {
        try {            
            return $this->file->get();

        } catch (Exception $e) {
            throw $e;
        }
    }

    public function fetchAndUpdateFile(): Collection
    {
        try {
            $fixturesData = $this->fetch();

            $this->file->write($fixturesData);
            
            return $fixturesData;

        } catch (Exception $e) {
            throw $e;
        }
    }
    
    private function parse(string $json): Collection
    {
        $decoded = json_decode($json)->response;

        return collect($decoded);
    }
}