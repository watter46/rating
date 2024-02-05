<?php declare(strict_types=1);

namespace App\UseCases\Player;

use Exception;
use Illuminate\Support\Facades\DB;

use App\Models\PlayerInfo;
use App\Http\Controllers\Util\PlayerImageFile;
use App\UseCases\Player\RegisterPlayerOfTeamBuilder;
use App\UseCases\Api\ApiFootball;
use App\UseCases\Api\SofaScore;


final readonly class RegisterPlayerOfTeamUseCase
{
    public function __construct(
        private PlayerImageFile $playerImage,
        private RegisterPlayerOfTeamBuilder $builder)
    {
        //
    }

    public function execute()
    {
        try {
            $FOOT_fetched = ApiFootball::squads()->fetch();
            $SOFA_fetched = SofaScore::playersOfTeam()->fetch();

            $playerInfoList = PlayerInfo::query()
                ->currentSeason()
                ->get();
                
            $data = $this->builder->build(
                $SOFA_fetched,
                $FOOT_fetched,
                $playerInfoList
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