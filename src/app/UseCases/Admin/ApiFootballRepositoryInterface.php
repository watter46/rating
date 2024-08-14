<?php declare(strict_types=1);

namespace App\UseCases\Admin;

use App\UseCases\Admin\Data\ApiFootball\FixturesData;
use App\UseCases\Admin\Data\ApiFootball\FixtureData\FixtureData;
use App\UseCases\Admin\Data\ApiFootball\SquadsData;
use App\UseCases\Admin\Fixture\Accessors\FixtureInfo;
use App\UseCases\Admin\Fixture\Accessors\FixtureInfos;


interface ApiFootballRepositoryInterface
{
    public function fetchFixtures(): FixturesData;
    public function preFetchFixtures(): FixtureInfos;
    public function fetchFixture(int $fixtureDataId): FixtureData;
    public function preFetchFixture(int $fixtureDataId): FixtureInfo;
    public function fetchSquads(): SquadsData;
    public function fetchLeagueImage(int $leagueId): string;
    public function fetchTeamImage(int $teamId): string;
}