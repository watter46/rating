<?php declare(strict_types=1);

namespace Database\Stubs\Fixture;

use App\Models\Fixture;
use Database\Stubs\Infrastructure\ApiFootball\MockApiFootballRepository;


class StubRegisterFixtureUseCase
{
    public function execute(int $id)
    {
        /** @var Fixture $fixture */
        $fixture = Fixture::where('external_fixture_id', $id)->first();

        /** @var MockApiFootballRepository $repository */
        $repository = app(MockApiFootballRepository::class);

        $data = $repository->fetchFixture($id);
        
        if (!$data->isFinished()) return;
        
        $fixture->updateFixture($data);
        
        $fixture->save();
    }
}