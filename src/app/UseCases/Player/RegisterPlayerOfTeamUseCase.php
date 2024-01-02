<?php declare(strict_types=1);

namespace App\UseCases\Player;

use Exception;
use Illuminate\Support\Facades\DB;

use App\Http\Controllers\Util\PlayerImageFile;
use App\Http\Controllers\Util\PlayerOfTeamFile;
use App\Http\Controllers\Util\SquadsFile;
use App\Models\ApiPlayer;
use App\Models\PlayerInfo;
use App\UseCases\Player\Builder\PlayerDataBuilder;
use App\UseCases\Player\Util\SofaScore;
use App\UseCases\Util\Season;


final readonly class RegisterPlayerOfTeamUseCase
{
    public function __construct(
        private PlayerInfo $player,
        private Season $season,
        private PlayerDataBuilder $builder,
        private PlayerOfTeamFile $playerOfTeam,
        private SquadsFile $squads,
        private PlayerImageFile $file)
    {
        //
    }

    // FootApiとApiFootballのidを持たせる
    public function execute()
    {
        try {
            $SOFA_fetched = $this->playerOfTeam->get();

            // $FOOT_fetched = SofaScore::playersOfTeam()->fetch();

            // $this->playerOfTeam->write($FOOT_fetched);

            $FOOT_fetched = $this->squads->get();
            
            $playerList = $this
                ->player
                ->select(['id', 'name', 'number', 'season'])
                ->where('season', $this->season->current())
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
                
                $this->player->upsert($data, $unique, $updateColumns);

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
        $playerIdList = $this
            ->player
            ->select(['foot_player_id', 'sofa_player_id'])
            ->where('season', $this->season->current())
            ->get()
            ->filter(fn (PlayerInfo $player) => $player->sofa_player_id)
            ->values()
            ->toArray();

        $missingIdList = $this->file->findMissingFiles($playerIdList);

        if (!$missingIdList) return;

        foreach($missingIdList as $player) {
            $image = SofaScore::playerPhoto($player['sofa_player_id'])->fetch();
            
            $this->file->write($player['foot_player_id'], $image);
        }
    }
}