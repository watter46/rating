<?php declare(strict_types=1);

namespace App\Listeners;

use Illuminate\Support\Facades\DB;

use App\Events\FixtureInfoRegistered;
use App\Models\PlayerInfo;
use App\UseCases\Admin\FlashLiveSportsRepositoryInterface;
use App\UseCases\Util\Season;
use Illuminate\Support\Collection;

class RegisterPlayerInfos
{
    /**
     * Create the event listener.
     */
    public function __construct(private FlashLiveSportsRepositoryInterface $repository)
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(FixtureInfoRegistered $event): void
    {
        $invalidPlayerInfos = $event->builder->invalidPlayerInfos();

        if ($invalidPlayerInfos->isEmpty()) return;
        
        $data = $invalidPlayerInfos
            ->map(function (Collection $playerInfo) {
                $flashLiveSportsPlayer = $this->repository
                    ->searchPlayer($playerInfo)
                    ->get();

                if (!$playerInfo['id']) {
                    return [
                        'name' => $playerInfo['name'],
                        'number' => $playerInfo['number'],
                        'season' => Season::current(),
                        'api_football_id' => $playerInfo['api_football_id'],
                        'flash_live_sports_id' => $flashLiveSportsPlayer['id'],
                        'flash_live_sports_image_id' => $flashLiveSportsPlayer['imageId']
                    ];
                }

                return [
                    'id' => $playerInfo['id'],
                    'name' => $playerInfo['name'],
                    'number' => $playerInfo['number'],
                    'season' => Season::current(),
                    'api_football_id' => $playerInfo['api_football_id'],
                    'flash_live_sports_id' => $flashLiveSportsPlayer['id'],
                    'flash_live_sports_image_id' => $flashLiveSportsPlayer['imageId']
                ];
            });

        DB::transaction(function () use ($data) {
            $unique = PlayerInfo::UPSERT_UNIQUE;
            
            PlayerInfo::upsert($data->toArray(), $unique);
        });
    }
}
