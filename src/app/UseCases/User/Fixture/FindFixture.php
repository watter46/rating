<?php declare(strict_types=1);

namespace App\UseCases\User\Fixture;

use Exception;
 
use App\Models\Fixture;
use App\UseCases\User\FixtureRequest;


final readonly class FindFixture
{    
    public function execute(FixtureRequest $request): Fixture
    {
        try {
            return $request
                ->buildFixture()
                ->loadAllInFixture()
                ->addMomLimit()
                ->addPlayers()
                ->get();

        } catch (Exception $e) {
            throw $e;
        }
    }
}