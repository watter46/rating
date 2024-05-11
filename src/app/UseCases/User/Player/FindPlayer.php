<?php declare(strict_types=1);

namespace App\UseCases\User\Player;

use Exception;

use App\Models\Player;
use App\UseCases\User\FixtureRequest;
use App\UseCases\User\FixtureValidator;


final readonly class FindPlayer
{
    public function __construct(private FixtureValidator $validator)
    {
        //
    }
    
    public function execute(FixtureRequest $request): Player
    {
        try {
            return $request
                ->buildFixture()
                ->assignPlayer($request)
                ->addColumnValidationToPlayer()
                ->getPlayer();

        } catch (Exception $e) {
            throw $e;
        }
    }
}