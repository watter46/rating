<?php declare(strict_types=1);

namespace App\UseCases\Admin;

use App\UseCases\Admin\Data\ApiFootball\SquadsData;
use App\UseCases\Admin\Fixture\Accessors\FixtureInfo;
use App\UseCases\Admin\Fixture\Accessors\FixtureInfos;


interface ApiFootballRepositoryInterface
{
    public function fetchFixtures(): FixtureInfos;
    public function fetchFixture(int $fixtureDataId): FixtureInfo;
    public function fetchSquads(): SquadsData;
    public function fetchLeagueImage(int $leagueId): string;
    public function fetchTeamImage(int $teamId): string;
}