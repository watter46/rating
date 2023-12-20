<?php declare(strict_types=1);

namespace App\UseCases\Player;

use Exception;
use Illuminate\Support\Facades\DB;

use App\Http\Controllers\Util\PlayerImageFile;
use App\Http\Controllers\Util\PlayerOfTeamFile;
use App\Http\Controllers\Util\SquadsFile;
use App\Models\Player;
use App\UseCases\Player\Builder\PlayerDataBuilder;
use App\UseCases\Player\Util\SofaScore;
use App\UseCases\Util\Season;


final readonly class RegisterPlayerOfTeamUseCase
{
    public function __construct(
        private Player $player,
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
            // $fetched = SofaScore::playerPhoto(769333)->fetch();
            // $fetched = SofaScore::playersOfTeam()->fetch();
            $SOFA_fetched = $this->playerOfTeam->get();

            $FOOT_fetched = $this->squads->get();
            
            // Modelにスコープを追加する(CurrentSeason)
            $playerList = $this
                ->player
                ->select(['id', 'name', 'number', 'season'])
                ->where('season', Season::current())
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

    public function registerImage()
    {
        $playerIdList = $this
            ->player
            ->select(['foot_player_id', 'sofa_player_id'])
            ->where('season', Season::current())
            ->get()
            ->filter(fn (Player $player) => $player->sofa_player_id)
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