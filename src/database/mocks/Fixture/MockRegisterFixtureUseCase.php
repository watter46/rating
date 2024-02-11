<?php declare(strict_types=1);

namespace Database\Mocks\Fixture;

use App\Models\Fixture;
use App\Http\Controllers\Util\FixtureFile;
use App\Http\Controllers\Util\PlayerFile;
use App\UseCases\Fixture\RegisterFixtureBuilder;


class MockRegisterFixtureUseCase
{
    public function __construct(
        private FixtureFile $fixture,
        private PlayerFile $player,
        private RegisterFixtureBuilder $builder
       )
    {
        //
    }

    public function execute(int $id)
    {

        /** @var Fixture $model */
        $model = Fixture::where('external_fixture_id', $id)->first();

        $fetched = $this->fixture->get($id);

        $data = $this->builder->build($fetched);

        $fixture = $model->updateFixture($data);
        
        $fixture->save();
    }
}