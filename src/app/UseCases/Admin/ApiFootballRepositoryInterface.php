<?php declare(strict_types=1);

namespace App\UseCases\Admin;

use App\UseCases\Admin\Fixture\FixtureInfoData\FixtureInfoData;
use App\UseCases\Admin\Fixture\FixtureInfosData\FixtureInfosData;
use App\UseCases\Admin\Data\ApiFootball\SquadsData;


interface ApiFootballRepositoryInterface
{
    public function fetchFixtures(): FixtureInfosData;
    public function fetchFixture(int $fixtureId): FixtureInfoData;
    public function fetchSquads(): SquadsData;
    public function fetchLeagueImage(int $leagueId): string;
    public function fetchTeamImage(int $teamId): string;
}