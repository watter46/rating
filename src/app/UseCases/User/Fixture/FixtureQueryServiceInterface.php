<?php declare(strict_types=1);

namespace App\UseCases\User\Fixture;

use App\UseCases\User\Domain\FixtureInfoId;
use App\UseCases\User\Fixture\FixtureMapper;


interface FixtureQueryServiceInterface
{
    public function find(FixtureInfoId $fixtureInfoId): FixtureMapper;
}