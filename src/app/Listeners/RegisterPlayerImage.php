<?php declare(strict_types=1);

namespace App\Listeners;

use App\Events\PlayerInfoRegistered;
use App\Http\Controllers\Util\PlayerImageFile;
use App\Models\PlayerInfo;
use App\UseCases\Admin\FlashLiveSportsRepositoryInterface;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

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
    public function handle(PlayerInfoRegistered $event): void
    {
        $event->teamSquad->imagePaths()
            ->each(function (PlayerInfo $playerInfo) {
                $image = $this->repository->fetchPlayerImage($playerInfo->path);
                
                $this->file->write($playerInfo->api_football_id, $image);
            });
    }
}
