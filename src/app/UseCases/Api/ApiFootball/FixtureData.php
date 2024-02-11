<?php declare(strict_types=1);

namespace App\UseCases\Api\ApiFootball;

use App\Http\Controllers\Util\FileInterface;
use App\Http\Controllers\Util\FixtureFile;
use App\Http\Controllers\Util\FixturesFile;
use Illuminate\Support\Collection;
use GuzzleHttp\Client;

use App\UseCases\Api\ApiDataInterface;
use App\UseCases\Util\Season;
use Exception;

readonly class FixtureData
{
    private const CHELSEA_TEAM_ID = 49;

    public function __construct(private FixtureFile $file)
    {
        //
    }

    public function fetch(int $fixtureId): Collection
    {
        try {            
            $client = new Client();

            $response = $client->request('GET', 'https://api-football-v1.p.rapidapi.com/v3/fixtures', [
                'query' => [
                    'id' => $fixtureId
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

    public function fetchOrGetFile(int $fixtureId): Collection
    {
        try {            
            if ($this->file->exists($fixtureId)) {
                return $this->file->get($fixtureId);
            }
            
            $fixtureData = $this->fetch($fixtureId);

            $this->file->write($fixtureId, $fixtureData);

            return $fixtureData;

          } catch (Exception $e) {
            throw $e;
        }
    }
    
    private function parse(string $json): Collection
    {
        $decoded = json_decode($json)->response;

        return collect($decoded[0]);
    }
}