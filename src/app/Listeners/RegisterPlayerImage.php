<?php declare(strict_types=1);

namespace App\Listeners;

use App\Events\FixtureInfoRegistered;
use App\Events\PlayerInfosRegistered;
use App\Http\Controllers\Util\PlayerImageFile;
use App\UseCases\Admin\Fixture\Accessors\LineupPlayer;
use App\UseCases\Admin\Fixture\Accessors\Player;
use App\UseCases\Admin\Fixture\Accessors\PlayerInfo;
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
    public function handle(FixtureInfoRegistered|PlayerInfosRegistered $event): void
    {
        if ($event instanceof FixtureInfoRegistered) {
            $fixtureInfo = $event->fixtureInfo;
        
            $invalidPlayers = $fixtureInfo
                ->refreshPlayerInfos()
                ->getInvalidImagePlayers();
                    
            if ($invalidPlayers->isEmpty()) return;
            
            $invalidPlayers
                ->each(function (LineupPlayer $player) {
                    $image = $this->repository->fetchPlayerImage($player->getPlayerInfo());
                    
                    $this->file->write($player->getPlayerId(), $image);
                });
            
            return;
        }
                
        $invalidPlayerInfos = $event->playerInfos->getInvalidImagePlayers();

        if ($invalidPlayerInfos->isEmpty()) return;

        $invalidPlayerInfos
            ->each(function (PlayerInfo $playerInfo) {
                $image = $this->repository->fetchPlayerImage($playerInfo);
                
                $this->file->write($playerInfo->getPlayerId(), $image);
            });
    }
}
