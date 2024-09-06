<?php declare(strict_types=1);

namespace App\Infrastructure\Fixture;

use App\Models\Fixture;
use App\UseCases\User\Domain\FixtureInfoId;
use App\UseCases\User\Fixture\FixtureMapper;
use App\UseCases\User\Fixture\FixtureQueryServiceInterface;


class FixtureQueryService implements FixtureQueryServiceInterface
{
    public function find(FixtureInfoId $fixtureInfoId): FixtureMapper
    {
        /** @var Fixture $fixture */
        $fixture = Fixture::query()
            ->selectWithout()
            ->byFixtureInfoId($fixtureInfoId)
            ->firstOrNew(['fixture_info_id' => $fixtureInfoId->get()])
            ->load([
                'fixtureInfo:id,score,teams,league,fixture,lineups',
                'fixtureInfo.playerInfos:id,api_player_id' ,
                'players:rating,mom,rate_count,fixture_id,player_info_id'
            ]);

        return (new FixtureMapper($fixture))->map();
    }
}