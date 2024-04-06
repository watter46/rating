<?php declare(strict_types=1);

namespace App\UseCases\User\Fixture;

use Illuminate\Support\Collection;

readonly class UserFixtureData
{
    public function __construct(private Collection $fixtureData)
    {
        
    }

    public static function create(Collection $fixtureData): self
    {
        return new self($fixtureData);
    }

    public function getPlayerIds(): Collection
    {
        return collect($this->fixtureData->get('lineups'))
            ->flatten(1)
            ->pluck('id');
    }
}