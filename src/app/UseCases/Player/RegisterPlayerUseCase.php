<?php declare(strict_types=1);

namespace App\UseCases\Player;

use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

use App\Models\PlayerInfo;
use App\Http\Controllers\Util\PlayerFile;
use App\Http\Controllers\Util\PlayerImageFile;


final readonly class RegisterPlayerUseCase
{
    public function __construct(
        private PlayerFile $playerFile,
        private PlayerImageFile $playerImage,
        private RegisterPlayerBuilder $builder)
    {
        //
    }

    public function execute(Collection $players)
    {
        try {
            $data = $this->builder->build($players);

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