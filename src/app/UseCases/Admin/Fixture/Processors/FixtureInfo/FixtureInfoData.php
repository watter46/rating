<?php declare(strict_types=1);

namespace App\UseCases\Admin\Fixture\Processors\FixtureInfo;

use Illuminate\Support\Collection;

use App\Models\FixtureInfo;


class FixtureInfoData
{
    private function __construct(
        private Collection $score,
        private Collection $teams,
        private Collection $league,
        private Collection $fixture,
        private Collection $lineups
    ) {
        
    }

    public static function create(FixtureInfo $fixtureInfo): self
    {
        return new self(
            $fixtureInfo->score,
            $fixtureInfo->teams,
            $fixtureInfo->league,
            $fixtureInfo->fixture,
            $fixtureInfo->lineups
        );
    }

    public function getTeamIds(): Collection
    {
        return $this->teams->pluck('id');
    }

    public function getLeagueId(): int
    {
        return $this->league->get('id');
    }

    public function getLeague(): Collection
    {
        return $this->league;
    }

    public function getScore(): Collection
    {
        return $this->score;
    }

    public function getFixture(): Collection
    {
        return $this->fixture;
    }

    public function getPlayedPlayers(): Collection
    {
        return $this->lineups->flatten(1);
    }

    public function lineupsExists(): bool
    {
        return $this->lineups->isNotEmpty();
    }
}