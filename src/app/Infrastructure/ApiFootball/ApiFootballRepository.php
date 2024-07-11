<?php declare(strict_types=1);

namespace App\Infrastructure\ApiFootball;

use Illuminate\Support\Facades\Http;

use App\UseCases\Admin\ApiFootballRepositoryInterface;
use App\UseCases\Admin\Fixture\FixtureInfoData\FixtureInfoData;
use App\UseCases\Admin\Fixture\FixtureInfosData\FixtureInfosData;
use App\UseCases\Admin\Data\ApiFootball\SquadsData;
use App\UseCases\Util\Season;


class ApiFootballRepository implements ApiFootballRepositoryInterface
{
    private function httpClient(string $url, ?array $queryParams = null): string
    {
        $response = Http::withHeaders([
            'X-RapidAPI-Host' => config('api-football.api-host'),
            'X-RapidAPI-Key'  => config('api-football.api-key')
        ])
        ->retry(1, 500)
        ->get($url, $queryParams);

        return $response->throw()->body();
    }

    public function fetchFixtures(): FixtureInfosData
    {
        $json = $this->httpClient('https://api-football-v1.p.rapidapi.com/v3/fixtures', [
                'season' => Season::current(),
                'team'   => config('api-football.chelsea-id')
            ]);

        $data = collect(json_decode($json)->response);

        return FixtureInfosData::create($data);
    }

    public function fetchFixture(int $fixtureId): FixtureInfoData
    {
        $json = $this->httpClient('https://api-football-v1.p.rapidapi.com/v3/fixtures', [
                'id' => $fixtureId
            ]);

        $data = collect(json_decode($json)->response[0]);
        
        return FixtureInfoData::create($data);
    }

    public function fetchSquads(): SquadsData
    {
        $json = $this->httpClient('https://api-football-v1.p.rapidapi.com/v3/players/squads', [
                'team' => config('api-football.chelsea-id')
            ]);

        $data = collect(json_decode($json)->response[0]);

        return SquadsData::create($data);
    }

    public function fetchLeagueImage(int $leagueId): string
    {
        return $this->httpClient("https://media-4.api-sports.io/football/leagues/$leagueId.png");
    }

    public function fetchTeamImage(int $teamId): string
    {
        return $this->httpClient("https://media-4.api-sports.io/football/teams/$teamId.png");
    }
}