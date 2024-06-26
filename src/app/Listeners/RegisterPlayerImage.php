<?php declare(strict_types=1);

namespace App\Listeners;

use Illuminate\Http\Client\RequestException;

use App\Events\FixtureInfoRegistered;
use App\Http\Controllers\Util\PlayerImageFile;
use App\UseCases\Admin\SofaScoreRepositoryInterface;


class RegisterPlayerImage
{
    /**
     * Create the event listener.
     */
    public function __construct(private PlayerImageFile $file, private SofaScoreRepositoryInterface $repository)
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(FixtureInfoRegistered $event): void
    {
        $invalidPlayerImageIds = $event->data->validated()->getInvalidPlayerImageIds();
                        
        if ($invalidPlayerImageIds->isEmpty()) return;
        
        $players = $event
            ->fixtureInfo
            ->playerInfos
            ->whereIn('foot_player_id', $invalidPlayerImageIds->toArray());
            
        foreach($players as $player) {
            try {
                if ($this->file->exists($player->foot_player_id)) {
                    continue;
                }
    
                $playerImage = $this->repository->fetchPlayerImage($player->sofa_player_id);
                
                $this->file->write($player->foot_player_id, $playerImage);

            } catch (RequestException $e) {
                continue;
            }
        }
    }
}
