<?php declare(strict_types=1);

namespace App\UseCases\User\Player;

use App\UseCases\User\PlayerInFixture;
use App\UseCases\User\PlayerInFixtureRequest;


class FetchMomCountUseCase
{    
    public function __construct(private PlayerInFixture $playerInFixture)
    {
        
    }
    
    public function execute(PlayerInFixtureRequest $request)
    {
        return $this->playerInFixture
            ->request($request)
            ->getMomCountAndLimit();
    }
}