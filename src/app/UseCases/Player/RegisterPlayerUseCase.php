<?php declare(strict_types=1);

namespace App\UseCases\Player;

use App\Http\Controllers\Util\PlayerFile;
use App\Http\Controllers\Util\PlayerImageFile;
use App\Models\PlayerInfo;
use App\UseCases\Player\Util\SofaScore;
use App\UseCases\Util\Season;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Collection;

final readonly class RegisterPlayerUseCase
{
    public function __construct(
        private PlayerInfo $player,
        private Season $season,
        private PlayerFile $playerFile,
        private PlayerImageFile $playerImage)
    {
        //
    }

    public function execute(Collection $players)
    {
        try {
            $data = $players
                ->map(function (array $player) {
                    // $fetched = SofaScore::findPlayer($player['name'])->fetch();

                    // $player = collect(json_decode($fetched)->data)
                    //     ->filter(function ($player) {
                    //         return $player->team->shortName === 'Chelsea';
                    //     })
                    //     ->toJson();

                    // $playerId = json_decode($player)[0]->id;
                        
                    // $this->playerFile->write($playerId, $player);

                    $fetched = $this->playerFile->get(1403055);

                    return collect([
                            'name' => $fetched->shortName,
                            'season' => $this->season->current(),
                            'number' => $fetched->jerseyNumber,
                            'foot_player_id' => $player['id'],
                            'sofa_player_id' => $fetched->id
                        ]);
                })
                ->toArray();
                
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
        $playerIdList = $this->player
            ->select(['foot_player_id', 'sofa_player_id'])
            ->where('season', $this->season->current())
            ->get()
            ->filter(fn (PlayerInfo $player) => $player->sofa_player_id)
            ->values()
            ->toArray();

        $missingIdList = $this->playerImage->findMissingFiles($playerIdList);

        if (!$missingIdList) return;

        foreach($missingIdList as $player) {
            $image = SofaScore::playerPhoto($player['sofa_player_id'])->fetch();
            
            $this->playerImage->write($player['foot_player_id'], $image);
        }
    }
}