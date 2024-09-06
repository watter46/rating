<?php declare(strict_types=1);

namespace App\Infrastructure\Factory;

use App\Models\Fixture as FixtureModel;
use App\UseCases\User\Domain\Fixture\Fixture;
use App\UseCases\User\Domain\Fixture\FixtureFactoryInterface;
use App\UseCases\User\Domain\Fixture\MomCount;
use App\UseCases\User\Domain\FixtureId;

class FixtureFactory implements FixtureFactoryInterface
{
    public function create(): Fixture
    {
        return new Fixture(
            FixtureId::create(),
            MomCount::create()
        );
    }

    public function reconstruct(FixtureModel $fixture): Fixture
    {
        return new Fixture(
            FixtureId::reconstruct($fixture->id),
            MomCount::create()
        );
    }
}