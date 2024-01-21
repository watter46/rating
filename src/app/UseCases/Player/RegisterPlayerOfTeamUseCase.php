<?php declare(strict_types=1);

namespace App\UseCases\Player;

use App\Http\Controllers\Util\PlayerFile;
use Exception;
use Illuminate\Support\Facades\DB;

use App\Http\Controllers\Util\PlayerImageFile;
use App\Http\Controllers\Util\PlayerOfTeamFile;
use App\Http\Controllers\Util\SquadsFile;
use App\Models\PlayerInfo;
use App\UseCases\Player\Builder\PlayerDataBuilder;


final readonly class RegisterPlayerOfTeamUseCase
{
    public function __construct(
        private PlayerDataBuilder $builder,
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

            // $this->playerOfTeam->write($SOFA_fetched);
            // $this->squads->write($FOOT_fetched);

            // $FOOT_fetched = $this->apiFootballFetcher->squads()->fetch();
            // $SOFA_fetched = $this->sofaScore->playersOfTeam()->fetch();

            $playerList = PlayerInfo::query()
                ->currentSeason()
                ->get()
                ->toArray();
                
            $data = $this->builder->build(
                $SOFA_fetched,
                $FOOT_fetched,
                $playerList
            );
 
            DB::transaction(function () use ($data) {
                $unique = ['id'];
                $updateColumns = ['name', 'number', 'season', 'foot_player_id', 'sofa_player_id'];
                
                PlayerInfo::upsert($data, $unique, $updateColumns);

                $this->registerImage();
            });
            

        } catch (Exception $e) {
            throw $e;
        }
    }
    
    /**
     * プレイヤーの画像を保存する
     *
     * @return void
     */
    private function registerImage()
    {
        $playerInfos = PlayerInfo::query()
            ->select(['foot_player_id', 'sofa_player_id'])
            ->currentSeason()
            ->get()
            ->filter(fn (PlayerInfo $player) => $player->sofa_player_id)
            ->values();
            
        $this->playerImage->registerAll($playerInfos);
    }
}