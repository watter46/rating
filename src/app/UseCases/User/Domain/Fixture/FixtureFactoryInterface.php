<?php declare(strict_types=1);

namespace App\UseCases\User\Domain\Fixture;

use App\Models\Fixture as FixtureModel;
use App\UseCases\User\Domain\Fixture\Fixture;


interface FixtureFactoryInterface
{
    public function create();
    public function reconstruct(FixtureModel $fixture): Fixture;
}