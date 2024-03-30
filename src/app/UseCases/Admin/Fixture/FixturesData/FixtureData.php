<?php declare(strict_types=1);

namespace App\UseCases\Admin\Fixture\FixturesData;

use App\Models\FixtureStatusType;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use stdClass;

class FixtureData
{
    /**
     * Fixturesデータ内のFixture
     *
     * @param  mixed $fixtureData
     * @return void
     */
    public function __construct(private stdClass $fixtureData)
    {
        //
    }

    public function getFixtureId(): int
    {
        return $this->fixtureData->fixture->id;
    }

    public function getDate(): Carbon
    {
        return Carbon::parse($this->fixtureData->fixture->date, 'UTC');
    }

    // Enumにするか検討
    public function getStatus(): string
    {
        return $this->fixtureData->fixture->status->long;
    }

    public function getLeagueId(): int
    {
        return $this->fixtureData->league->id;
    }

    public function getLeagueName(): string
    {
        return $this->fixtureData->league->name;
    }

    public function getSeason(): int
    {
        return $this->fixtureData->league->season;
    }

    public function getRound(): string
    {
        return $this->fixtureData->league->round;
    }

    public function getHomeTeam(): Collection
    {
        return collect([
            'id'   => $this->fixtureData->teams->home->id,
            'name' => $this->fixtureData->teams->home->name
        ]);
    }

    public function getAwayTeam(): Collection
    {
        return collect([
            'id'   => $this->fixtureData->teams->away->id,
            'name' => $this->fixtureData->teams->away->name
        ]);
    }
}