<?php declare(strict_types=1);

namespace App\Listeners;

use App\Events\FixtureInfoRegistered;
use App\UseCases\Admin\Player\RegisterPlayerInfos as RegisterPlayerInfosUseCase;


class RegisterPlayerInfos
{
    /**
     * Create the event listener.
     */
    public function __construct(private RegisterPlayerInfosUseCase $registerPlayerInfos)
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(FixtureInfoRegistered $event): void
    {
        $invalidPlayers = $event->data->validated()->getInvalidPlayers();
        
        if ($invalidPlayers->isEmpty()) return;
        
        $this->registerPlayerInfos->execute($invalidPlayers, $event->fixtureInfo->playerInfos);
    }
}