<?php declare(strict_types=1);

namespace App\Infrastructure\FlashLiveSports;

use App\Http\Controllers\Util\TeamSquadFile;
use App\UseCases\Admin\Data\FlashLiveSports\TeamSquad;
use App\UseCases\Admin\FlashLiveSportsRepositoryInterface;
use Illuminate\Support\Facades\Http;

class FlashLiveSportsRepository implements FlashLiveSportsRepositoryInterface
{
    private function httpClient(string $url, ?array $queryParams = null): string
    {
        $response = Http::withHeaders([
            'X-RapidAPI-Host' => config('flash-live-sports.api-host'),
            'X-RapidAPI-Key'  => config('flash-live-sports.api-key')
        ])
        ->retry(1, 500)
        ->get($url, $queryParams);

        return $response->throw()->body();
    }

    public function fetchTeamSquad(): TeamSquad
    {
        $json = $this->httpClient('https://flashlive-sports.p.rapidapi.com/v1/teams/squad', [
                'sport_id' => config('flash-live-sports.sport-id'),
                'team_id'  => config('flash-live-sports.chelsea-id'),
                'locale'   => config('flash-live-sports.locale')
            ]);

        $data = collect(json_decode($json)->data);
        
        return TeamSquad::create($data);
    }
}