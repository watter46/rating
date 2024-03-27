<?php declare(strict_types=1);

namespace App\UseCases\Api\ApiFootball;

use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

use App\Http\Controllers\Util\SquadsFile;


readonly class SquadsFetcher
{
    public function __construct(private SquadsFile $file)
    {
        //
    }

    public function fetch(): Collection
    {
        try {
            $response = Http::withHeaders([
                'X-RapidAPI-Host' => config('api-football.api-host'),
                'X-RapidAPI-Key'  => config('api-football.api-key')
            ])
            ->retry(1, 500)
            ->get('https://api-football-v1.p.rapidapi.com/v3/players/squads', [
                'team' => config('api-football.chelsea-id')
            ]);

            return $this->parse($response->throw()->body());

        } catch (Exception $e) {
            throw $e;
        }
    }

    public function getFile(): Collection
    {
        return $this->file->get();
    }

    public function fetchOrGetFile(): Collection
    {
        try {            
            if ($this->file->exists()) {
                return $this->file->get();
            }

            $squadsData = $this->fetch();

            $this->file->write($squadsData);
            
            return $squadsData;

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