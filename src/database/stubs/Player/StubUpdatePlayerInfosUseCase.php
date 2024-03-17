<?php declare(strict_types=1);

namespace Database\Stubs\Player;

use Exception;
use Illuminate\Support\Facades\DB;

use App\Models\PlayerInfo;
use App\Http\Controllers\Util\PlayerOfTeamFile;
use App\Http\Controllers\Util\SquadsFile;
use App\UseCases\Admin\Player\UpdatePlayerInfos\PlayersOfTeamData;
use App\UseCases\Admin\Player\UpdatePlayerInfos\SquadsData;
use App\UseCases\Admin\Player\UpdatePlayerInfos\UpdatePlayerInfosDataBuilder;


class StubUpdatePlayerInfosUseCase
{
    public function __construct(
        private UpdatePlayerInfosDataBuilder $builder,
        private PlayerOfTeamFile $playerOfTeam,
        private SquadsFile $squads)
    {
        //
    }

    public function execute()
    {
        try {
            $squadsData = $this->squads->get();
            $playersOfTeamData = $this->playerOfTeam->get();

            $data = $this->builder->build(
                SquadsData::build($squadsData),
                PlayersOfTeamData::build($playersOfTeamData),
                PlayerInfo::currentSeason()->get()
            );
 
            DB::transaction(function () use ($data) {
                $unique = ['id'];
                $updateColumns = ['name', 'number', 'season', 'foot_player_id', 'sofa_player_id'];
                
                PlayerInfo::upsert($data, $unique, $updateColumns);
            });
            
        } catch (Exception $e) {
            throw $e;
        }
    }
}