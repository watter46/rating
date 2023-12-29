<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

use App\Models\ApiPlayer;
use App\Http\Controllers\Util\PlayerOfTeamFile;
use App\Http\Controllers\Util\SquadsFile;
use App\UseCases\Player\Builder\PlayerDataBuilder;
use App\UseCases\Util\Season;


class ApiPlayerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        /** @var PlayerOfTeamFile $playerOfTeam */
        $playerOfTeam = app(PlayerOfTeamFile::class);
        
        /** @var SquadsFile $squads */
        $squads = app(SquadsFile::class);

        $SOFA_fetched = $playerOfTeam->get();

        $FOOT_fetched = $squads->get();
        
        $playerList = ApiPlayer::query()
            ->select(['id', 'name', 'number', 'season'])
            ->where('season', Season::current())
            ->get()
            ->toArray();

        /** @var PlayerDataBuilder $builder */
        $builder = app(PlayerDataBuilder::class);
            
        $data = $builder->build(
            $SOFA_fetched,
            $FOOT_fetched,
            $playerList
        );

        $unique = ['id'];
        $updateColumns = ['name', 'number', 'season', 'foot_player_id', 'sofa_player_id'];
        
        ApiPlayer::upsert($data, $unique, $updateColumns);
    }
}
