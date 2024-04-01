<?php declare(strict_types=1);

namespace App\Infrastructure\ApiFootball;

use App\Models\Fixture;
use App\UseCases\Admin\ApiFootballRepositoryInterface;
use App\UseCases\Admin\Fixture\FixtureData\FixtureData;
use App\UseCases\Admin\Fixture\FixturesData\Formatter\FixtureDataFormatter;
use App\UseCases\Admin\Fixture\FixturesData\FixturesDataBuilder;
use App\UseCases\Util\Season;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

class ApiFootballRepository implements ApiFootballRepositoryInterface
{
    public function __construct(private FixturesDataBuilder $fixturesDataBuilder)
    {
        //
    }

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

    public function fetchFixtures(): Collection
    {
        $json = $this->httpClient('https://api-football-v1.p.rapidapi.com/v3/fixtures', [
            'season' => Season::current(),
            'team'   => config('api-football.chelsea-id')
        ]);

        $data = collect(json_decode($json)->response);

        // return $this->fixturesDataBuilder->build(new FixtureDataFormatter($data));
    }

    public function fetchFixture(int $fixtureId): FixtureData
    {
        dd($fixtureId);
        $json = $this->httpClient('https://api-football-v1.p.rapidapi.com/v3/fixtures', [
            'id' => $fixtureId
        ]);

        return FixtureData::create(collect(json_decode($json)->response[0]));
    }

    public function fetchSquads(): Collection
    {
        $json = $this->httpClient('https://api-football-v1.p.rapidapi.com/v3/players/squads', [
            'team' => config('api-football.chelsea-id')
        ]);

        return collect(json_decode($json)->response[0]);
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