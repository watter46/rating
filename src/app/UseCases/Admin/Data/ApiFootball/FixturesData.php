<?php declare(strict_types=1);

namespace App\UseCases\Admin\Data\ApiFootball;

use App\UseCases\Admin\Data\ApiFootball\FixtureData\FixtureData;
use Illuminate\Support\Collection;


class FixturesData
{    
    /**
     * __construct
     *
     * @param  Collection<FixtureData> $fixturesData
     * @return void
     */
    private function __construct(private Collection $fixturesData)
    {
        //
    }

    public static function create(Collection $fixturesData): self
    {
        return new self($fixturesData->map(function ($fixtureData) {
            return FixtureData::create(collect($fixtureData));
        }));
    }
    
    /**
     * keyByFixtureId
     *
     * @return Collection<FixtureData>
     */
    public function keyByFixtureId(): Collection
    {
        return $this->fixturesData
            ->keyBy(function (FixtureData $fixtureData) {
                return $fixtureData->getFixtureId();
            });
    }

    public function get(int $fixtureId): ?FixtureData
    {
        return $this->fixturesData
            ->first(function (FixtureData $fixtureData) use ($fixtureId) {
                return $fixtureData->equal($fixtureId);
            });
    }

    public function getFixtureIds()
    {
        return $this->fixturesData
            ->map(function (FixtureData $fixtureData) {
                return $fixtureData->getFixtureId();
            });
    }
}