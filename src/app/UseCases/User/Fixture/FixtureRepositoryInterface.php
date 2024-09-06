<?php declare(strict_types=1);

namespace App\UseCases\User\Fixture;

use App\UseCases\User\Domain\Fixture\Fixture;
use App\UseCases\User\Domain\FixtureId;


interface FixtureRepositoryInterface
{
    public function find(FixtureId $fixtureId): Fixture;
    public function save(Fixture $fixture);
}