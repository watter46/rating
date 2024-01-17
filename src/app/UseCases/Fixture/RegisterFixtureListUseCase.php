<?php declare(strict_types=1);

namespace App\UseCases\Fixture;

use App\Http\Controllers\Util\FixturesFile;
use App\Http\Controllers\Util\LeagueImageFile;
use App\Http\Controllers\Util\TeamImageFile;
use Exception;
use Illuminate\Support\Facades\DB;

use App\Models\Fixture;
use App\UseCases\Player\Builder\FixtureDataListBuilder;
use App\UseCases\Player\Util\ApiFootballFetcher;
use App\UseCases\Util\Season;

final readonly class RegisterFixtureListUseCase
{
    const CHELSEA_TEAM_ID = 49;
    const END_STATUS = 'Match Finished';
    
    public function __construct(
        private Fixture $fixture,
        private Season $season,
        private TeamImageFile $teamImageFile,
        private LeagueImageFile $leagueImageFile,
        private FixturesFile $file,
        private FixtureDataListBuilder $builder)
    {
        //
    }

    public function execute()
    {
        try {
            // $fetched = ApiFootballFetcher::fixtures()->fetch();
            $fetched = $this->file->get();
            // dd($fetched);

            // $this->file->write(json_encode($fetched));
            
            $this->registerTeamImage($fetched);
            $this->registerLeagueImage($fetched); 
            
            $fixtureList = $this
                ->fixture
                ->select(['id', 'external_fixture_id'])
                ->where('season', $this->season->current())
                ->get()
                ->toArray();

            $data = $this->builder->build($fetched, $fixtureList);
            
            DB::transaction(function () use ($data) {
                $unique = ['id'];
                $updateColumns = ['date', 'is_end'];

                $this->fixture->upsert($data, $unique, $updateColumns);
            });

        } catch (Exception $e) {
            throw $e;
        }
    }
    
    /**
     * リーグの画像を保存する
     *
     * @param  array $fetched
     * @return void
     */
    private function registerLeagueImage(array $fetched)
    {
        $uniqueLeague = collect($fetched)
            ->map(fn ($fixture) => $fixture->league)
            ->unique('id');

        foreach($uniqueLeague as $league) {
            if ($this->leagueImageFile->exists($league->id)) continue;

            $image = ApiFootballFetcher::leagueImage($league->id)->fetchImage();

            $this->leagueImageFile->write($league->id, $image);
        }
    }
    
    /**
     * チームの画像を保存する
     *
     * @param  array $fetched
     * @return void
     */
    private function registerTeamImage(array $fetched)
    {
        $uniqueTeams = collect($fetched)
            ->flatMap(function ($fixture) {
                return [
                    $fixture->teams->away,
                    $fixture->teams->home
                ];
            })
            ->unique('id');

        foreach($uniqueTeams as $team) {
            if ($this->teamImageFile->exists($team->id)) continue;

            $image = ApiFootballFetcher::teamImage($team->id)->fetchImage();
            
            $this->teamImageFile->write($team->id, $image);
        }
    }
}