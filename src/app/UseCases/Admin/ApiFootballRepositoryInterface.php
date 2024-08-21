<?php declare(strict_types=1);

namespace App\UseCases\Admin;

use App\UseCases\Admin\Fixture\Accessors\FixtureInfo;
use App\UseCases\Admin\Fixture\Accessors\FixtureInfos;
use App\UseCases\Admin\Fixture\Accessors\PlayerInfos;


interface ApiFootballRepositoryInterface
{
    public function fetchFixtures(): FixtureInfos;
    public function fetchFixture(int $fixtureDataId): FixtureInfo;
    public function fetchSquads(): PlayerInfos;
    public function fetchLeagueImage(int $leagueId): string;
    public function fetchTeamImage(int $teamId): string;
}