<?php declare(strict_types=1);

namespace Database\Stubs\Fixture;

use App\Models\Fixture;
use App\Http\Controllers\Util\FixtureFile;
use App\UseCases\Util\FixtureData;


class StubRegisterFixtureUseCase
{
    public function __construct(
        private FixtureFile $fixture,
        private FixtureData $fixtureData
       )
    {
        //
    }

    public function execute(int $id)
    {

        /** @var Fixture $model */
        $model = Fixture::where('external_fixture_id', $id)->first();

        $fetched = $this->fixture->get($id);

        $data = $this->fixtureData->build($fetched);

        $fixture = $model->updateFixture($data);
        
        $fixture->save();
    }
}