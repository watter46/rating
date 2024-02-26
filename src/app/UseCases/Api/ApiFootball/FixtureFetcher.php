<?php declare(strict_types=1);

namespace App\UseCases\Api\ApiFootball;

use Illuminate\Support\Collection;
use GuzzleHttp\Client;
use Exception;

use App\UseCases\Util\FixtureData;


readonly class FixtureFetcher
{
    public function __construct(private FixtureData $fixtureData)
    {
        //
    }
    
    /**
     * FixtureデータをApiFootballから取得する
     *
     * @param  int $fixtureId
     * @return Collection
     */
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
    
    /**
     * Fixtureのファイルを取得する
     *
     * @param  int $fixtureId
     * @return Collection
     */
    public function getFile(int $fixtureId): Collection
    {
        try {
            return $this->fixtureData->getByFile($fixtureId);
        } catch (Exception $e) {
            throw $e;
        }
    }
    
    /**
     *  ApiまたはファイルからFixtureデータを取得する
     *
     * @param  int $fixtureId
     * @return Collection
     */
    public function fetchOrGetFile(int $fixtureId): Collection
    {
        try {
            if (!$this->shouldFetch($fixtureId)) {
                return $this->getFile($fixtureId);
            }
            
            $fixtureData = $this->fetch($fixtureId);

            $this->fixtureData->store($fixtureId, $fixtureData);

            return $fixtureData;

          } catch (Exception $e) {
            throw $e;
        }
    }
    
    /**
     * ApiからFixtureデータを取得するか判定する
     *
     * @param  int $fixtureId
     * @return bool
     */
    private function shouldFetch(int $fixtureId): bool
    {
        if (!$this->fixtureData->exists($fixtureId)) {
            return true;
        }

        if (!$this->fixtureData->isFinished($fixtureId)) {
            return true;
        }

        return false;
    }
        
    /**
     * ApiからFixtureのデータのみ取得する
     *
     * @param  string $json
     * @return Collection
     */
    private function parse(string $json): Collection
    {
        $decoded = json_decode($json)->response;

        return collect($decoded[0]);
    }
}