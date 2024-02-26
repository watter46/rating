<?php declare(strict_types=1);

namespace Database\Stubs\Fixture;

use App\Models\Fixture;
use App\UseCases\Admin\Fixture\FixturesDataBuilder;
use App\UseCases\Api\ApiFootball\FixturesData;
use Illuminate\Database\Eloquent\Collection;


class StubRegisterFixturesUseCase
{
    public function __construct(
        private FixturesDataBuilder $builder,
        private FixturesData $fixturesData)
    {
        //
    }

    public function execute()
    {
        $fixturesData = $this->fixturesData->getFile();
        
        /** @var Collection<int, Fixture> */
        $fixtureList = Fixture::query()
            ->select(['id', 'external_fixture_id'])
            ->currentSeason()
            ->get();

        $data = $this->builder->build($fixturesData, $fixtureList);
        
        $unique = ['id'];
        $updateColumns = ['date', 'status', 'score'];

        Fixture::upsert($data, $unique, $updateColumns);
    }
}