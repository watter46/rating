<?php declare(strict_types=1);

namespace Database\Stubs\Infrastructure\ApiFootball;

use App\Http\Controllers\Util\FixtureFile;
use App\Http\Controllers\Util\FixturesFile;
use App\Http\Controllers\Util\LeagueImageFile;
use App\Http\Controllers\Util\SquadsFile;
use App\Http\Controllers\Util\TeamImageFile;
use App\UseCases\Admin\ApiFootballRepositoryInterface;
use App\UseCases\Admin\Fixture\FixtureInfoData\FixtureInfoData;
use App\UseCases\Admin\Fixture\FixtureInfosData\FixtureInfosData;
use App\UseCases\Admin\Player\SquadsData\SquadsData;


class MockApiFootballRepository implements ApiFootballRepositoryInterface
{
    public function __construct(
        private FixturesFile $fixturesFile,
        private FixtureFile $fixtureFile,
        private SquadsFile $squadsFile,
        private LeagueImageFile $leagueImageFile,
        private TeamImageFile $teamImageFile
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
        return $this->leagueImageFile->get($leagueId);
    }

    public function fetchTeamImage(int $teamId): string
    {
        return $this->teamImageFile->get($teamId);
    }
}