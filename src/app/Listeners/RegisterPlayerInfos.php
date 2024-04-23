<?php declare(strict_types=1);

namespace App\Listeners;

use App\Events\FixtureInfoRegistered;
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
    public function handle(FixtureInfoRegistered $event): void
    {
        $invalidPlayerIds = $event->data->validated()->getInvalidPlayers();
        
        if ($invalidPlayerIds->isEmpty()) return;

        $this->registerPlayer->execute($invalidPlayerIds);
    }
}