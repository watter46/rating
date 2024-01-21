<?php declare(strict_types=1);

namespace Database\Mocks\Fixture;

use App\Http\Controllers\Util\FixturesFile;
use App\Http\Controllers\Util\LeagueImageFile;
use App\Http\Controllers\Util\TeamImageFile;

use App\Models\Fixture;
use App\UseCases\Player\Builder\FixtureDataListBuilder;


class MockRegisterFixtureListUseCase
{
    const CHELSEA_TEAM_ID = 49;
    const END_STATUS = 'Match Finished';
    
    public function __construct(
        private TeamImageFile $teamImage,
        private LeagueImageFile $leagueImage,
        private FixturesFile $fixtures,
        private FixtureDataListBuilder $builder)
    {
        //
    }

    public function execute()
    {
        $fixtures = $this->fixtures->get();
        
        $this->teamImage->registerAll($fixtures);
        $this->leagueImage->registerAll($fixtures);
        
        $fixtureList = Fixture::query()
            ->select(['id', 'external_fixture_id'])
            ->currentSeason()
            ->get()
            ->toArray();

        $data = $this->builder->build($fixtures, $fixtureList);

        $unique = ['id'];
        $updateColumns = ['date', 'is_end'];

        Fixture::upsert($data, $unique, $updateColumns);
    }
}