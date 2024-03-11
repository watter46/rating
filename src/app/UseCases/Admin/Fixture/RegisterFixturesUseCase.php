<?php declare(strict_types=1);

namespace App\UseCases\Admin\Fixture;

use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

use App\Models\Fixture;
use App\UseCases\Api\ApiFootball\FixturesFetcher;
use App\UseCases\Admin\Fixture\FixturesDataBuilder;
use App\UseCases\Fixtures\Fixtures;

final readonly class RegisterFixturesUseCase
{    
    public function __construct(
        private FixturesDataBuilder $builder,
        private FixturesFetcher $fixturesFetcher,
        private Fixtures $fixtures)
    {
        //
    }

    public function execute()
    {
        try {            
            $fixturesData = $this->fixturesFetcher->fetchAndUpdateFile();
            
            /** @var Collection<int, Fixture> */
            $fixtureList = Fixture::query()
                ->select(['id', 'external_fixture_id'])
                ->currentSeason()
                ->get();

            $data = $this->builder->build($fixturesData, $fixtureList);
            
            DB::transaction(function () use ($data) {
                $unique = ['id'];
                $updateColumns = ['date', 'status', 'score'];

                Fixture::upsert($data, $unique, $updateColumns);
            });

            $this->fixtures->registered($fixturesData);

        } catch (Exception $e) {
            throw $e;
        }
    }
}