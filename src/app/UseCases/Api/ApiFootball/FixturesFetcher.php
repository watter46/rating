<?php declare(strict_types=1);

namespace App\UseCases\Api\ApiFootball;

use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

use App\UseCases\Util\Season;
use App\Http\Controllers\Util\FixturesFile;


readonly class FixturesFetcher
{
    public function __construct(private FixturesFile $file)
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
            ->get('https://api-football-v1.p.rapidapi.com/v3/fixtures', [
                'season' => Season::current(),
                'team'   => config('api-football.chelsea-id')
            ]);

            return $this->parse($response->throw()->body());

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