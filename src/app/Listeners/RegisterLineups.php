<?php declare(strict_types=1);

namespace App\Listeners;

use App\Events\FixtureInfoRegistered;
use App\Models\FixtureInfo;
use App\Models\PlayerInfo;


class RegisterLineups
{
    public function handle(FixtureInfoRegistered $event): void
    {
        $fixtureInfo = $event->fixtureInfo;
        
        $playerIds = $fixtureInfo->getPlayerIds();

        $playerInfoIds = PlayerInfo::query()
            ->whereIn('api_football_id', $playerIds)
            ->pluck('id');
        
        FixtureInfo::query()
            ->select('id')
            ->find($fixtureInfo->getId())
            ->playerInfos()
            ->sync($playerInfoIds);
    }
}
