<?php declare(strict_types=1);

namespace App\UseCases\Player;

use Exception;
use Illuminate\Support\Facades\DB;

use App\Models\Fixture;
use App\UseCases\Player\Util\ApiFootballFetcher;


final readonly class RegisterFixtureUseCase
{
    const CHELSEA_TEAM_ID = 49;
    const END_STATUS = 'Match Finished';
    
    public function __construct(private Fixture $fixture)
    {
        //
    }

    public function execute()
    {
        try {
            $json = ApiFootballFetcher::fixtures()->fetch();
            
            $data = collect(json_decode($json))
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
                        'season' => $fixture->league->season,
                        'is_end' => $fixture->fixture->status->long === self::END_STATUS,
                        'is_home' => $is_home,
                        'home' => $fixture->score->fulltime->home,
                        'away' => $fixture->score->fulltime->away,
                        'first_half_at' => date('Y-m-d H:i', $fixture->fixture->periods->first),
                        'second_half_at' => date('Y-m-d H:i', $fixture->fixture->periods->second),
                    ];
                })->toArray();
            
            DB::transaction(function () use ($data) {
                $unique = ['id'];
                $updateColumns = ['home', 'away', 'first_half_at', 'second_half_at'];

                $this->fixture->upsert($data, $unique, $updateColumns);
            });

        } catch (Exception $e) {
            throw $e;
        }
    }
}