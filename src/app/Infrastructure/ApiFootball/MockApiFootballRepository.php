<?php declare(strict_types=1);

namespace App\Infrastructure\ApiFootball;

use Illuminate\Support\Facades\Http;
use App\UseCases\Util\Season;

use App\Http\Controllers\Util\FixtureFile;
use App\Http\Controllers\Util\FixturesFile;
use App\Http\Controllers\Util\LeagueImageFile;
use App\Http\Controllers\Util\SquadsFile;
use App\Http\Controllers\Util\TeamImageFile;
use App\Http\Controllers\Util\TestFixtureInfoFile;
use App\Http\Controllers\Util\TestFixtureInfosFile;
use App\Http\Controllers\Util\TestLeagueImageFile;
use App\Http\Controllers\Util\TestTeamImageFile;
use App\UseCases\Admin\ApiFootballRepositoryInterface;
use App\UseCases\Admin\Fixture\Accessors\FixtureInfo;
use App\UseCases\Admin\Fixture\Accessors\FixtureInfos;
use App\UseCases\Admin\Fixture\Accessors\PlayerInfos;
use App\UseCases\Admin\Fixture\Accessors\Api\ApiSquad;


class MockApiFootballRepository implements ApiFootballRepositoryInterface
{
    public function __construct(
        private TestFixtureInfosFile $testFixtureInfosFile,
        private FixturesFile $fixturesFile,
        private TestFixtureInfoFile $testFixtureInfoFile,
        private FixtureFile $fixtureFile,
        private SquadsFile $squadsFile,
        private LeagueImageFile $leagueImageFile,
        private TestLeagueImageFile $testLeagueImageFile,
        private TeamImageFile $teamImageFile,
        private TestTeamImageFile $testTeamImageFile
    ) {}

    private function isTest(): bool
    {
        return env('APP_ENV') === 'testing';
    }

    private function isSeed(): bool
    {
        return config('seeder.status');
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

    public function fetchFixtures(): FixtureInfos
    {
        if ($this->isTest()) {
            return FixtureInfos::create($this->testFixtureInfosFile->get());
        }

        if ($this->isSeed()) {
            return FixtureInfos::create($this->fixturesFile->get());
        } 

        if ($this->fixturesFile->exists()) {
            return FixtureInfos::create($this->fixturesFile->get());
        }
        
        $json = $this->httpClient('https://api-football-v1.p.rapidapi.com/v3/fixtures', [
            'season' => Season::current(),
            'team'   => config('api-football.chelsea-id')
        ]);

        $data = collect(json_decode($json)->response);

        $this->fixturesFile->write($data);

        return FixtureInfos::create($data);
    }

    public function fetchFixture(int $fixtureDataId): FixtureInfo
    {
        if ($this->fixtureFile->exists($fixtureDataId) &&
            $this->fixtureFile->isFinished($fixtureDataId) ||
            $this->isTest()) {
            
            return FixtureInfo::create($this->fixtureFile->get($fixtureDataId));
        }
        
        if ($this->isSeed()) {
            return FixtureInfo::create($this->fixtureFile->get($fixtureDataId));
        }
        
        $json = $this->httpClient('https://api-football-v1.p.rapidapi.com/v3/fixtures', [
            'id' => $fixtureDataId
        ]);

        $data = collect(json_decode($json)->response[0]);
        
        $this->fixtureFile->write($fixtureDataId, $data);

        return FixtureInfo::create($data);
    }

    public function fetchSquads(): PlayerInfos
    {
        if ($this->isTest()) {
            return PlayerInfos::fromSquad(ApiSquad::create($this->squadsFile->get()));
        }
        
        if ($this->isSeed()) {
            if ($this->squadsFile->exists()) {
                return PlayerInfos::fromSquad(ApiSquad::create($this->squadsFile->get()));
            }

            dd('not exists');
        }
        
        if ($this->squadsFile->exists()) {
            return PlayerInfos::fromSquad(ApiSquad::create($this->squadsFile->get()));
        }
        
        $json = $this->httpClient('https://api-football-v1.p.rapidapi.com/v3/players/squads', [
            'team' => config('api-football.chelsea-id')
        ]);

        $data = collect(json_decode($json)->response[0]);

        $this->squadsFile->write($data);

        return PlayerInfos::fromSquad(ApiSquad::create($this->squadsFile->get()));
    }

    public function fetchLeagueImage(int $leagueId): string
    {
        if ($this->isTest()) {
            return $this->testLeagueImageFile->get();
        }
        
        if ($this->leagueImageFile->exists($leagueId)) {
            return $this->leagueImageFile->get($leagueId);
        }

        dd('league');

        return $this->httpClient("https://media-4.api-sports.io/football/leagues/$leagueId.png");
    }

    public function fetchTeamImage(int $teamId): string
    {
        if ($this->isTest()) {
            return $this->testTeamImageFile->get();
        }
        
        if ($this->teamImageFile->exists($teamId)) {
            return $this->teamImageFile->get($teamId);
        }

        dd('team');

        return $this->httpClient("https://media-4.api-sports.io/football/teams/$teamId.png");
    }
}