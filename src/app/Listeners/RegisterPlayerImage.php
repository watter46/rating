<?php declare(strict_types=1);

namespace App\Listeners;

use App\Events\FixtureInfoRegistered;
use App\Events\PlayerInfoRegistered;
use App\Http\Controllers\Util\PlayerImageFile;
use App\Models\PlayerInfo;
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
        $invalidPlayerInfos = $event->builder->getInvalidPlayerImageIds();
        
        $invalidPlayerInfos
            ->each(function (PlayerInfo $playerInfo) {
                $image = $this->repository->fetchPlayerImage($playerInfo->flash_live_sports_image_id);
                
                $this->file->write($playerInfo->api_football_id, $image);
            });
    }
}
