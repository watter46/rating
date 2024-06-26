<?php declare(strict_types=1);

namespace App\UseCases\Admin\Player;

use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

use App\Models\PlayerInfo;
use App\UseCases\Admin\SofaScoreRepositoryInterface;

final readonly class RegisterPlayerInfos
{
    public function __construct(
        private SofaScoreRepositoryInterface $sofaScoreRepository,
        private RegisterPlayerBuilder $builder)
    {
        //
    }
    
    /**
     * execute
     *
     * @param  Collection $invalidPlayers
     * @param  Collection<PlayerInfo> $playerInfos
     * @return void
     */
    public function execute(Collection $invalidPlayers, Collection $playerInfos)
    {
        try {            
            $playersData = $invalidPlayers
                ->map(function (array $player) {
                    return $this->sofaScoreRepository->fetchPlayer($player);
                });

            $data = $this->builder->build($playersData, $playerInfos);

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