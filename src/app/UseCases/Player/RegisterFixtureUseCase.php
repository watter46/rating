<?php declare(strict_types=1);

namespace App\UseCases\Player;

use App\Http\Controllers\Util\FixturesFile;
use App\Http\Controllers\Util\LeagueImageFile;
use App\Http\Controllers\Util\TeamImageFile;
use Exception;
use Illuminate\Support\Facades\DB;

use App\Models\Fixture;
use App\UseCases\Player\Util\ApiFootballFetcher;


final readonly class RegisterFixtureUseCase
{
    const CHELSEA_TEAM_ID = 49;
    const END_STATUS = 'Match Finished';
    
    public function __construct(
        private Fixture $fixture,
        private TeamImageFile $teamImageFile,
        private LeagueImageFile $leagueImageFile,
        private FixturesFile $file)
    {
        //
    }

    public function execute()
    {
        try {
            $fetched = ApiFootballFetcher::fixtures()->fetch();
            
            $this->registerTeamImage($fetched);
            $this->registerLeagueImage($fetched); 
            
            $fixtures = $this
                ->fixture
                ->select(['id', 'external_fixture_id'])
                ->where('season', 2023)
                ->get()
                ->toArray();
            
            $data = collect($fetched)
                ->map(function ($fixture) {

                    $is_home = $fixture->teams->home->id === self::CHELSEA_TEAM_ID;
                    
                    $opponent_id = $is_home
                        ? $fixture->teams->away->id
                        : $fixture->teams->home->id;
                    
                    $opponent_name = $is_home
                        ? $fixture->teams->away->name
                        : $fixture->teams->home->name;
                    
                    return [
                        'external_fixture_id' => $fixture->fixture->id,
                        'external_team_id' => $opponent_id,
                        'team_name' => $opponent_name,
                        'external_league_id' => $fixture->league->id,
                        'league_name' => $fixture->league->name,
                        'round' => $fixture->league->round,
                        'season' => $fixture->league->season,
                        'is_end' => $fixture->fixture->status->long === self::END_STATUS,
                        'is_home' => $is_home,
                        'home' => $fixture->score->fulltime->home,
                        'away' => $fixture->score->fulltime->away,
                        'first_half_at' => date('Y-m-d H:i', $fixture->fixture->periods->first),
                        'second_half_at' => date('Y-m-d H:i', $fixture->fixture->periods->second),
                    ];
                });

            $upsertData = $fixtures
                ? $data
                    ->zip(collect($fixtures))
                    ->map(function ($fixture) {
                        return array_merge($fixture[0], $fixture[1]);
                    })
                    ->toArray()
                : $data->toArray();

            DB::transaction(function () use ($upsertData) {
                $unique = ['id'];
                $updateColumns = ['home', 'away', 'first_half_at', 'second_half_at'];

                $this->fixture->upsert($upsertData, $unique, $updateColumns);
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