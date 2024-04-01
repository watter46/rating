<?php declare(strict_types=1);

namespace App\Listeners;

use GuzzleHttp\Exception\ClientException;

use App\Events\FixtureRegistered;
use App\Http\Controllers\Util\PlayerImageFile;
use App\Models\PlayerInfo;
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
    public function handle(FixtureRegistered $event): void
    {
        $invalidPlayerImageIds = $event->fixtureData->validated()->getInvalidPlayerImageIds();
        
        if ($invalidPlayerImageIds->isEmpty()) return;

        $players = PlayerInfo::query()
            ->select(['foot_player_id', 'sofa_player_id'])
            ->whereIn('foot_player_id', $invalidPlayerImageIds->toArray())
            ->get();

        foreach($players as $player) {
            try {
                if ($this->file->exists($player->foot_player_id)) {
                    continue;
                }
    
                $playerImage = $this->repository->fetchPlayerImage($player->sofa_player_id);
    
                $this->file->write($player->foot_player_id, $playerImage);

            } catch (ClientException $e) {
                continue;
            }
        }
    }
}
