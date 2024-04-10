<?php declare(strict_types=1);

namespace App\UseCases\Admin;

use App\UseCases\Admin\Fixture\FixtureData\FixtureData;
use App\UseCases\Admin\Fixture\FixturesData\FixturesData;
use App\UseCases\Admin\Player\SquadsData\SquadsData;


interface ApiFootballRepositoryInterface
{
    public function fetchFixtures(): FixturesData;
    public function fetchFixture(int $fixtureId): FixtureData;
    public function fetchSquads(): SquadsData;
    public function fetchLeagueImage(int $leagueId): string;
    public function fetchTeamImage(int $teamId): string;
}