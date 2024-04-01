<?php declare(strict_types=1);

namespace App\UseCases\Admin;

use App\UseCases\Admin\Fixture\FixtureData\FixtureData;
use Illuminate\Support\Collection;

interface ApiFootballRepositoryInterface
{
    public function fetchFixtures(): Collection;
    public function fetchFixture(int $fixtureId): FixtureData;
    public function fetchSquads(): Collection;
    public function fetchLeagueImage(int $leagueId): string;
    public function fetchTeamImage(int $teamId): string;
}