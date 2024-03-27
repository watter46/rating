<?php declare(strict_types=1);

namespace App\UseCases\Api\SofaScore;

use Exception;
use GuzzleHttp\Client;
use Illuminate\Support\Collection;

use App\Http\Controllers\Util\PlayerOfTeamFile;
use Illuminate\Support\Facades\Http;

class PlayersOfTeamFetcher
{
    public function __construct(private PlayerOfTeamFile $file)
    {
        //
    }

    public function fetch(): Collection
    {
        try {           
            $response = Http::withHeaders([
                'X-RapidAPI-Host' => config('sofa-score.api-host'),
                'X-RapidAPI-Key'  => config('sofa-score.api-key')
            ])
            ->retry(1, 500)
            ->get('https://sofascores.p.rapidapi.com/v1/teams/players', [
                'team_id' => (string) config('sofa-score.chelsea-id')
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

            $playersOfTeamData = $this->fetch();

            $this->file->write($playersOfTeamData);
            
            return $playersOfTeamData;

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