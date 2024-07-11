<?php declare(strict_types=1);

namespace App\Listeners;

use App\Events\FixtureInfoRegistered;
use App\Models\FixtureInfo;
use App\Models\PlayerInfo;


class RegisterLineups
{
    public function handle(FixtureInfoRegistered $event): void
    {
        $builder = $event->builder;

        $apiFootballIds = $builder->getApiFootballIds();
        
        $playerInfoIds = PlayerInfo::query()
            ->whereIn('api_football_id', $apiFootballIds)
            ->pluck('id');
        
        $builder->getFixtureInfo()
            ->playerInfos()
            ->sync($playerInfoIds);
    }
}
