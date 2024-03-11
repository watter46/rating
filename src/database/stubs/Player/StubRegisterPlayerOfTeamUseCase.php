<?php declare(strict_types=1);

namespace Database\Stubs\Player;

use Exception;
use Illuminate\Support\Facades\DB;

use App\Models\PlayerInfo;
use App\Http\Controllers\Util\PlayerImageFile;
use App\Http\Controllers\Util\PlayerOfTeamFile;
use App\Http\Controllers\Util\SquadsFile;
use App\UseCases\Player\RegisterPlayerOfTeamBuilder;


class StubRegisterPlayerOfTeamUseCase
{
    public function __construct(
        private RegisterPlayerOfTeamBuilder $builder,
        private PlayerOfTeamFile $playerOfTeam,
        private SquadsFile $squads,
        private PlayerImageFile $playerImage)
    {
        //
    }

    public function execute()
    {
        try {
            $SOFA_fetched = $this->playerOfTeam->get();
            $FOOT_fetched = $this->squads->get();

            $playerList = PlayerInfo::query()
                ->currentSeason()
                ->get();

            $data = $this->builder->build(
                $SOFA_fetched,
                $FOOT_fetched,
                $playerList
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