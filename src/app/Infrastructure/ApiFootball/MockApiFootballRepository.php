<?php declare(strict_types=1);

namespace App\Infrastructure\ApiFootball;

use App\Http\Controllers\Util\FixtureFile;
use App\Http\Controllers\Util\FixturesFile;
use App\Http\Controllers\Util\SquadsFile;
use App\Http\Controllers\Util\TestFixtureInfoFile;
use App\Http\Controllers\Util\TestFixtureInfosFile;
use Illuminate\Support\Facades\Http;

use App\UseCases\Admin\ApiFootballRepositoryInterface;
use App\UseCases\Admin\Fixture\FixtureInfoData\FixtureInfoData;
use App\UseCases\Admin\Fixture\FixtureInfosData\FixtureInfosData;
use App\UseCases\Admin\Player\SquadsData\SquadsData;
use App\UseCases\Util\Season;


class MockApiFootballRepository implements ApiFootballRepositoryInterface
{
    public function __construct(
        private TestFixtureInfosFile $testFixtureInfosFile,
        private FixturesFile $fixturesFile,
        private TestFixtureInfoFile $testFixtureInfoFile,
        private FixtureFile $fixtureFile,
        private SquadsFile $squadsFile
    ) {}

    private function isTest(): bool
    {
        return env('APP_ENV') === 'testing';
    }

    private function httpClient(string $url, ?array $queryParams = null): string
    {
        if ($this->isTest()) {
            dd('test');
        }

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
        if ($this->isTest()) {
            return FixtureInfosData::create($this->testFixtureInfosFile->get());
        }
        
        if ($this->fixturesFile->exists()) {
            return FixtureInfosData::create($this->fixturesFile->get());
        }
        
        $json = $this->httpClient('https://api-football-v1.p.rapidapi.com/v3/fixtures', [
            'season' => Season::current(),
            'team'   => config('api-football.chelsea-id')
        ]);

        $data = collect(json_decode($json)->response);

        $this->fixturesFile->write($data);

        return FixtureInfosData::create($data);
    }

    public function fetchFixture(int $fixtureId): FixtureInfoData
    {
        if ($this->fixtureFile->exists($fixtureId) || $this->isTest()) {
            return FixtureInfoData::create($this->fixtureFile->get($fixtureId));
        }
        
        $json = $this->httpClient('https://api-football-v1.p.rapidapi.com/v3/fixtures', [
            'id' => $fixtureId
        ]);

        $data = collect(json_decode($json)->response[0]);
        
        $this->fixtureFile->write($fixtureId, $data);

        return FixtureInfoData::create($data);
    }

    public function fetchSquads(): SquadsData
    {
        if ($this->isTest()) {
            // return
        }
        
        if ($this->squadsFile->exists()) {
            return SquadsData::create($this->squadsFile->get());
        }
        
        $json = $this->httpClient('https://api-football-v1.p.rapidapi.com/v3/players/squads', [
            'team' => config('api-football.chelsea-id')
        ]);

        $data = collect(json_decode($json)->response[0]);

        $this->squadsFile->write($data);

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