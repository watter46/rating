<?php declare(strict_types=1);

namespace App\UseCases\Fixture;

use Exception;
use Illuminate\Support\Facades\DB;

use App\Models\Fixture;
use App\Http\Controllers\Util\FixturesFile;
use App\Http\Controllers\Util\LeagueImageFile;
use App\Http\Controllers\Util\TeamImageFile;
use App\UseCases\Fixture\Builder\FixtureDataListBuilder;
use App\UseCases\Player\Util\ApiFootball;
use App\UseCases\Util\Season;


final readonly class RegisterFixtureListUseCase
{    
    public function __construct(
        private TeamImageFile $teamImage,
        private LeagueImageFile $leagueImage,
        private FixturesFile $file,
        private FixtureDataListBuilder $builder)
    {
        //
    }

    public function execute()
    {
        try {
            // $fetched = ApiFootball::fixtures()->fetch();
            $fetched = $this->file->get();
            // dd($fetched);

            // $this->file->write(json_encode($fetched));

            $this->teamImage->registerAll($fetched);
            
            $this->leagueImage->registerAll($fetched);
            
            $fixtureList = Fixture::query()
                ->select(['id', 'external_fixture_id'])
                ->where('season', Season::current())
                ->get()
                ->toArray();

            $data = $this->builder->build($fetched, $fixtureList);
            
            DB::transaction(function () use ($data) {
                $unique = ['id'];
                $updateColumns = ['date', 'is_end'];

                Fixture::upsert($data, $unique, $updateColumns);
            });

        } catch (Exception $e) {
            throw $e;
        }
    }
}