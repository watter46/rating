<?php declare(strict_types=1);

namespace App\Listeners;

use App\Events\FixtureRegistered;
use App\UseCases\User\Player\RegisterPlayerUseCase;


class RegisterPlayerInfos
{
    /**
     * Create the event listener.
     */
    public function __construct(private RegisterPlayerUseCase $registerPlayer)
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(FixtureRegistered $event): void
    {
        $invalidPlayerIds = $event->fixtureData->validated()->getInvalidPlayers();
        
        if ($invalidPlayerIds->isEmpty()) return;

        $this->registerPlayer->execute($invalidPlayerIds);
    }
}