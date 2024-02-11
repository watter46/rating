<?php declare(strict_types=1);

namespace App\Listeners;

use App\Events\FixtureRegistered;
use App\UseCases\Player\FilterUnregisteredPlayers;
use App\UseCases\Player\RegisterPlayerUseCase;
use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;


class CheckOrRegisterPlayerInfos
{
    /**
     * Create the event listener.
     */
    public function __construct(
        private FilterUnregisteredPlayers $filterUnregisteredPlayers,
        private RegisterPlayerUseCase $registerPlayer)
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(FixtureRegistered $event): void
    {
        try {
            $fixture = $event->model;

            $players = $this->filterUnregisteredPlayers->execute($fixture);
            
            if ($players->isEmpty()) return;

            $this->registerPlayer->execute($players);
            
        } catch (Exception $e) {
            throw $e;
        }
    }
}
