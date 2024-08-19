<?php declare(strict_types=1);

namespace App\Listeners;

use Illuminate\Support\Facades\DB;

use App\Events\FixtureInfoRegistered;
use App\Models\PlayerInfo as PlayerInfoModel;
use App\UseCases\Admin\Fixture\Accessors\LineupPlayer;
use App\UseCases\Admin\FlashLiveSportsRepositoryInterface;
use App\UseCases\Admin\Fixture\Accessors\PlayerInfo;


class RegisterPlayerInfos
{
    public function __construct(private FlashLiveSportsRepositoryInterface $repository)
    {
        //
    }

    public function handle(FixtureInfoRegistered $event): void
    {        
        $invalidPlayers = $event->fixtureInfo->getInvalidPlayers();

        if ($invalidPlayers->isEmpty()) {
            return;
        }
        
        $data = $invalidPlayers
            ->map(function (LineupPlayer $player) {
                $playerInfo = $player->getPlayerInfo();

                if ($playerInfo->needsUpdate()) {
                    return $playerInfo->updateFromPlayer($player);
                }
                
                $flashPlayer = $this->repository->searchPlayer($player->getPlayerInfo());

                return $playerInfo->updateFlash($flashPlayer);
            })
            ->map(fn (PlayerInfo $playerInfo) => $playerInfo->toArray());
            
        DB::transaction(function () use ($data) {
            $unique = PlayerInfoModel::UPSERT_UNIQUE;
            
            PlayerInfoModel::upsert($data->toArray(), $unique);
        });
    }
}
