<?php declare(strict_types=1);

namespace App\UseCases\Admin\Fixture;

use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

use App\Models\Fixture;
use App\UseCases\Api\ApiFootball\FixturesData;
use App\UseCases\Admin\Fixture\FixturesDataBuilder;


final readonly class RegisterFixturesUseCase
{    
    public function __construct(
        private FixturesDataBuilder $builder,
        private FixturesData $fixturesData)
    {
        //
    }

    public function execute()
    {
        try {            
            $fixturesData = $this->fixturesData->fetchAndUpdateFile();
            
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

        } catch (Exception $e) {
            throw $e;
        }
    }
}