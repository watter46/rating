<?php declare(strict_types=1);

namespace App\UseCases\Api\SofaScore;

use App\Http\Controllers\Util\PlayerFile;
use Illuminate\Support\Collection;
use GuzzleHttp\Client;
use Exception;


readonly class PlayerFetcher
{
    public function __construct(private PlayerFile $file)
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

    public function fetchOrGetFile(array $player): Collection
    {
        try {            
            if ($this->file->exists($player['id'])) {
                return $this->getFile($player);
            }

            $playerData = $this->fetch($player['name']);
            
            $this->file->write($player['id'], $playerData);

            return $playerData;

          } catch (Exception $e) {
            throw $e;
        }
    }

    public function getFile(array $player): Collection
    {
        try {            
            return $this->file->get($player['id']);

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