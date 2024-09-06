<?php

namespace App\UseCases\User\Player;

use App\UseCases\User\Domain\Fixture\Fixture;
use App\UseCases\User\Domain\FixtureId;
use App\UseCases\User\Domain\Player\PlayerId;
use App\UseCases\User\Fixture\FixtureRepositoryInterface;

class Rate
{
    public function __construct(
        private FixtureRepositoryInterface $fixtureRepository,
        private PlayerRepositoryInterface $playerRepository)
    {
        
    }

    /**
     * Fixtureを
     *
     * @param string $fixtureInfoId
     * @param string $playerId
     * @return void
     */
    public function execute(string $fixtureInfoId, string $playerId)
    {
        $fixture = $this->fixtureRepository->find(FixtureId::reconstruct($fixtureId));

        $player = $this->playerRepository->find($playerId)->rate();
        
        $this->playerRepository->save($player);
    }
}