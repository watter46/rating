<?php declare(strict_types=1);

namespace App\Listeners;

use App\Events\FixtureInfoRegistered;
use App\Models\PlayerInfo;

class RegisterLineups
{
    public function handle(FixtureInfoRegistered $event): void
    {
        $fixtureInfo = $event->fixtureInfo;
        
        $footPlayerIds = $fixtureInfo->lineups->flatten(1)->pluck('id');
        
        $playerInfoIds = PlayerInfo::whereIn('foot_player_id', $footPlayerIds)->pluck('id');
        
        $fixtureInfo->playerInfos()->sync($playerInfoIds);
    }
}
