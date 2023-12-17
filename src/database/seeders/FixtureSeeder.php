<?php declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Fixture;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Http\Controllers\Util\FixturesFile;

class FixtureSeeder extends Seeder
{
    const CHELSEA_TEAM_ID = 49;
    const END_STATUS = 'Match Finished';

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        /** @var FixturesFile $file */
        $file = app(FixturesFile::class);

        $json = $file->get();
        
        $data = collect($json)
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
        
        $unique = ['id'];
        $updateColumns = ['home', 'away', 'first_half_at', 'second_half_at'];

        (new Fixture)->upsert($data, $unique, $updateColumns);
    }
}
