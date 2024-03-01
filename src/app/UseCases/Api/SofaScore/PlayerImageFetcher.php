<?php declare(strict_types=1);

namespace App\UseCases\Api\SofaScore;

use Illuminate\Support\Collection;
use GuzzleHttp\Client;
use Exception;

use App\Http\Controllers\Util\PlayerImageFile;


readonly class PlayerImageFetcher
{
    public function __construct(private PlayerImageFile $file)
    {
        //
    }

    public function fetch(int $playerId): string
    {
        try {            
            $client = new Client();

            $response = $client->request('GET', 'https://sofascores.p.rapidapi.com/v1/players/photo', [
                'query' => [
                    'player_id' => (string) $playerId
                ],
                'delay' => 500,
                'headers' => [
                    'X-RapidAPI-Host' => config('sofa-score.api-host'),
                    'X-RapidAPI-Key'  => config('sofa-score.api-key')
                ]
            ]);

            return $response->getBody()->getContents();

          } catch (Exception $e) {
            throw $e;
        }
    }

    public function fetchOrGetFile(int $playerId): string
    {
        try {            
            if ($this->file->exists($playerId)) {
                return $this->file->get($playerId);
            }
            
            $playerImage = $this->fetch($playerId);

            $this->file->write($playerId, $playerImage);

            return $playerImage;

          } catch (Exception $e) {
            throw $e;
        }
    }
}