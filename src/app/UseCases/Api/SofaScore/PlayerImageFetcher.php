<?php declare(strict_types=1);

namespace App\UseCases\Api\SofaScore;

use GuzzleHttp\Exception\ClientException;
use Exception;

use App\Http\Controllers\Util\PlayerImageFile;
use Illuminate\Support\Facades\Http;

readonly class PlayerImageFetcher
{
    public function __construct(private PlayerImageFile $file)
    {
        //
    }

    public function fetch(int $playerId): string
    {
        try {            
            $response = Http::withHeaders([
                'X-RapidAPI-Host' => config('sofa-score.api-host'),
                'X-RapidAPI-Key'  => config('sofa-score.api-key')
            ])
            ->retry(1, 500)
            ->get('https://sofascores.p.rapidapi.com/v1/players/photo', [
                'player_id' => (string) $playerId
            ]);

            return $response->throw()->body();

        } catch (ClientException $e) {
            throw $e;
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
            
            return $this->fetch($playerId);

        } catch (ClientException $e) {
            throw $e;

        }catch (Exception $e) {
            throw $e;
        }
    }
}