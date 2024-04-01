<?php

namespace Database\Stubs\Infrastructure\ApiFootball;

use App\Http\Controllers\Util\FixtureFile;
use App\Http\Controllers\Util\FixturesFile;
use App\UseCases\Admin\ApiFootballRepositoryInterface;
use App\UseCases\Admin\Fixture\FixtureData\FixtureData;
use App\UseCases\Admin\Fixture\FixturesData\Formatter\FixtureDataFormatter;
use App\UseCases\Admin\Fixture\FixturesData\FixturesDataBuilder;
use App\UseCases\Util\Season;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

class MockApiFootballRepository implements ApiFootballRepositoryInterface
{
    public function __construct(private FixtureFile $fixtureFile)
    {
        
    }

    public function fetchFixtures(): Collection
    {
        return $this->fixturesDataBuilder->build($this->fixturesFile->get());
    }

    public function fetchFixture(int $fixtureId): FixtureData
    {
        return FixtureData::create($this->fixtureFile->get($fixtureId));
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