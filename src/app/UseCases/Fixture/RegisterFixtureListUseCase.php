<?php declare(strict_types=1);

namespace App\UseCases\Fixture;

use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

use App\Models\Fixture;
use App\Http\Controllers\Util\FixturesFile;
use App\Http\Controllers\Util\LeagueImageFile;
use App\Http\Controllers\Util\TeamImageFile;
use App\UseCases\Api\ApiFootball\ApiFootballFetcher;
use App\UseCases\Api\ApiFootball\FixturesData;
use App\UseCases\Fixture\RegisterFixtureListBuilder;


final readonly class RegisterFixtureListUseCase
{    
    public function __construct(
        private TeamImageFile $teamImage,
        private LeagueImageFile $leagueImage,
        private FixturesFile $file,
        private RegisterFixtureListBuilder $builder,
        private FixturesData $fixturesData)
    {
        //
    }

    public function execute()
    {
        try {            
            $fixturesData = $this->fixturesData->fetchOrGetFile();
            
            /** @var Collection<int, Fixture> */
            $fixtureList = Fixture::query()
                ->select(['id', 'external_fixture_id'])
                ->currentSeason()
                ->get();

            $data = $this->builder->build($fixturesData, $fixtureList);
            
            DB::transaction(function () use ($data) {
                $unique = ['id'];
                $updateColumns = ['date'];

                Fixture::upsert($data, $unique, $updateColumns);
            });

        } catch (Exception $e) {
            throw $e;
        }
    }
}