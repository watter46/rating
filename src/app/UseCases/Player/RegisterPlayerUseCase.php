<?php declare(strict_types=1);

namespace App\UseCases\Player;

use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

use App\Models\PlayerInfo;
use App\Http\Controllers\Util\PlayerFile;
use App\Http\Controllers\Util\PlayerImageFile;
use App\UseCases\Player\Util\SofaScore;
use App\UseCases\Util\Season;


final readonly class RegisterPlayerUseCase
{
    public function __construct(
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
                ->map(function (Collection $player) {
                    $fetched = SofaScore::findPlayer($player['player']['name'])->fetch();
                    
                    $filtered = collect(json_decode($fetched)->data)
                        ->filter(function ($player) {
                            return $player->team->shortName === 'Chelsea'
                                || $player->team->nameCode === 'CFC';
                        });

                    if ($filtered->isEmpty()) {
                        return collect([
                            'name' => $player['player']['name'],
                            'season' => $this->season->current(),
                            'number' => $player['player']['number'],
                            'foot_player_id' => $player['player']['id'],
                            'sofa_player_id' => null
                        ]);
                    }

                    $newPlayer = json_decode($filtered->toJson())[0];
                                        
                    $data = collect([
                            'name' => $newPlayer->shortName,
                            'season' => Season::current(),
                            'number' => $newPlayer->jerseyNumber,
                            'foot_player_id' => $player['player']['id'],
                            'sofa_player_id' => $newPlayer->id
                        ]);
                    
                    return $player->get('model')->isEmpty()
                        ? $data
                        : $data->merge(['id' => $player['model']->first()->id]);
                })
                ->toArray();
                
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