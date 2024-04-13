<?php declare(strict_types=1);

namespace App\UseCases\User\Player;

use Exception;

use App\Models\Player;
use App\UseCases\User\PlayerInFixture;
use App\UseCases\User\PlayerInFixtureRequest;


final readonly class FetchPlayerUseCase
{
    public function __construct(private PlayerInFixture $playerInFixture)
    {
        //
    }
    
    public function execute(PlayerInFixtureRequest $request): Player
    {
        try {
            return $this->playerInFixture
                ->request($request)
                ->addCanRateToPlayer()
                ->getPlayer();

        } catch (Exception $e) {
            throw $e;
        }
    }
}