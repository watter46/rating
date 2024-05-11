<?php declare(strict_types=1);

namespace Database\Stubs\Fixture;

use App\Models\FixtureInfo;
use App\Models\PlayerInfo;
use Database\Stubs\Infrastructure\ApiFootball\MockApiFootballRepository;


class StubRegisterFixtureInfo
{
    public function execute(int $id)
    {
        /** @var FixtureInfo $fixtureInfo */
        $fixtureInfo = FixtureInfo::where('external_fixture_id', $id)->first();

        /** @var MockApiFootballRepository $repository */
        $repository = app(MockApiFootballRepository::class);

        $data = $repository->fetchFixture($id);
        
        if (!$data->isFinished()) return;
        
        $fixtureInfo->updateLineups($data);
        
        $fixtureInfo->save();
        
        $footPlayerIds = $fixtureInfo->lineups->flatten(1)->pluck('id');

        $playerInfoIds = PlayerInfo::whereIn('foot_player_id', $footPlayerIds)->pluck('id');
        
        $fixtureInfo->playerInfos()->sync($playerInfoIds);
    }
}