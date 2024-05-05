<?php declare(strict_types=1);

namespace App\UseCases\User\Player;

use App\UseCases\User\FixtureRequest;


class FetchMomCount
{
    public function execute(FixtureRequest $request)
    {
        return $request->buildFixture()
            ->getMomCountAndLimit();
    }
}