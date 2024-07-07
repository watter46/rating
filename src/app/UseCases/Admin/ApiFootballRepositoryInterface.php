<?php declare(strict_types=1);

namespace App\UseCases\Admin;

use App\UseCases\Admin\Data\ApiFootball\FixturesData;
use App\UseCases\Admin\Data\ApiFootball\FixtureData\FixtureData;
use App\UseCases\Admin\Data\ApiFootball\SquadsData;


interface ApiFootballRepositoryInterface
{
    public function fetchFixtures(): FixturesData;
    public function fetchFixture(int $fixtureDataId): FixtureData;
    public function fetchSquads(): SquadsData;
    public function fetchLeagueImage(int $leagueId): string;
    public function fetchTeamImage(int $teamId): string;
}