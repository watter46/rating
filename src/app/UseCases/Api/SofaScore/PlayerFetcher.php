<?php declare(strict_types=1);

namespace App\UseCases\Api\SofaScore;

use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

use App\Http\Controllers\Util\PlayerFile;


readonly class PlayerFetcher
{
    public function __construct(private PlayerFile $file)
    {
        //
    }

    public function fetch(string $playerName): Collection
    {
        try {
            $response = Http::withHeaders([
                'X-RapidAPI-Host' => config('sofa-score.api-host'),
                'X-RapidAPI-Key'  => config('sofa-score.api-key')
            ])
            ->retry(1, 500)
            ->get('https://sofascores.p.rapidapi.com/v1/search/multi', [
                'query' => $playerName,
                'group' => 'players'
            ]);

            return $this->parse($response->throw()->body());

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