<?php declare(strict_types=1);

namespace Database\Stubs\Fixture;

use App\Models\FixtureInfo;
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
    }
}