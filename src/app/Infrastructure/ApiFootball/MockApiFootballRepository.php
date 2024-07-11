<?php declare(strict_types=1);

namespace App\Infrastructure\ApiFootball;

use App\Http\Controllers\Util\FixtureFile;
use App\Http\Controllers\Util\FixturesFile;
use App\Http\Controllers\Util\SquadsFile;
use App\Http\Controllers\Util\TestFixtureInfoFile;
use App\Http\Controllers\Util\TestFixtureInfosFile;
use App\Http\Controllers\Util\TestLeagueImageFile;
use App\Http\Controllers\Util\TestTeamImageFile;
use Illuminate\Support\Facades\Http;

use App\UseCases\Admin\ApiFootballRepositoryInterface;
use App\UseCases\Admin\Data\ApiFootball\FixtureData\FixtureData;
use App\UseCases\Admin\Data\ApiFootball\FixturesData;
use App\UseCases\Admin\Data\ApiFootball\SquadsData;
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

    public function fetchFixtures(): FixturesData
    {
        if ($this->isTest()) {
            return FixturesData::create($this->testFixtureInfosFile->get());
        }
        
        if ($this->fixturesFile->exists()) {
            return FixturesData::create($this->fixturesFile->get());
        }
        
        $json = $this->httpClient('https://api-football-v1.p.rapidapi.com/v3/fixtures', [
            'season' => Season::current(),
            'team'   => config('api-football.chelsea-id')
        ]);

        $data = collect(json_decode($json)->response);

        $this->fixturesFile->write($data);

        return FixturesData::create($data);
    }

    public function fetchFixture(int $fixtureDataId): FixtureData
    {
        if ($this->fixtureFile->exists($fixtureDataId) || $this->isTest()) {
            return FixtureData::create($this->fixtureFile->get($fixtureDataId));
        }
        
        $json = $this->httpClient('https://api-football-v1.p.rapidapi.com/v3/fixtures', [
            'id' => $fixtureDataId
        ]);

        $data = collect(json_decode($json)->response[0]);
        
        $this->fixtureFile->write($fixtureDataId, $data);

        return FixtureData::create($data);
    }

    public function fetchSquads(): SquadsData
    {
        if ($this->isTest()) {
            //
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
        $file = new TestLeagueImageFile;

        return $file->get();
    }

    public function fetchTeamImage(int $teamId): string
    {
        $file = new TestTeamImageFile;

        return $file->get();
    }
}