<?php declare(strict_types=1);

namespace Database\Stubs\Infrastructure\ApiFootball;

use Illuminate\Support\Collection;

use App\Http\Controllers\Util\FixtureFile;
use App\Http\Controllers\Util\FixturesFile;
use App\Http\Controllers\Util\SquadsFile;
use App\UseCases\Admin\ApiFootballRepositoryInterface;
use App\UseCases\Admin\Fixture\FixtureData\FixtureData;
use App\UseCases\Admin\Fixture\FixtureInfoData\FixtureInfoData;
use App\UseCases\Admin\Fixture\FixtureInfosData\FixtureInfosData;
use App\UseCases\Admin\Player\SquadsData\SquadsData;

class MockApiFootballRepository implements ApiFootballRepositoryInterface
{
    public function __construct(
        private FixturesFile $fixturesFile,
        private FixtureFile $fixtureFile,
        private SquadsFile $squadsFile
    ) {
        //
    }

    public function fetchFixtures(): FixtureInfosData
    {
        return FixtureInfosData::create($this->fixturesFile->get());
    }

    public function fetchFixture(int $fixtureId): FixtureInfoData
    {
        return FixtureInfoData::create($this->fixtureFile->get($fixtureId));
    }

    public function fetchSquads(): SquadsData
    {
        return SquadsData::create($this->squadsFile->get());
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