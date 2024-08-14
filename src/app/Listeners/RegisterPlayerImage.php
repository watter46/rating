<?php declare(strict_types=1);

namespace App\Listeners;

use App\Events\FixtureInfoRegistered;
use App\Events\PlayerInfoRegistered;
use App\Http\Controllers\Util\PlayerImageFile;
use App\Models\PlayerInfo;
use App\UseCases\Admin\Fixture\Accessors\Player;
use App\UseCases\Admin\FlashLiveSportsRepositoryInterface;

class RegisterPlayerImage
{
    /**
     * Create the event listener.
     */
    public function __construct(
        private PlayerImageFile $file,
        private FlashLiveSportsRepositoryInterface $repository
    ) {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(FixtureInfoRegistered $event): void
    {
        $fixtureInfo = $event->fixtureInfo;
        
        $invalidPlayers = $fixtureInfo
            ->refreshPlayerInfos()
            ->getInvalidImagePlayers();
                
        if ($invalidPlayers->isEmpty()) return;
        
        $invalidPlayers
            ->each(function (Player $player) {
                $image = $this->repository->fetchPlayerImage($player);
                
                $this->file->write($player->getPlayerId(), $image);
            });
    }
}
