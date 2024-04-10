<?php declare(strict_types=1);

namespace App\UseCases\Admin\Player;

use Exception;
use Illuminate\Support\Facades\DB;

use App\Models\PlayerInfo;
use App\UseCases\Admin\ApiFootballRepositoryInterface;
use App\UseCases\Admin\SofaScoreRepositoryInterface;
use App\UseCases\Admin\Player\UpdatePlayerInfosDataBuilder;


final readonly class UpdatePlayerInfosUseCase
{
    public function __construct(
        private ApiFootballRepositoryInterface $apiFootballRepository,
        private SofaScoreRepositoryInterface $sofaScoreRepository,
        private UpdatePlayerInfosDataBuilder $builder
    ) {
        //
    }

    public function execute(): void
    {
        try {
            $squadsData = $this->apiFootballRepository->fetchSquads();
            $playersOfTeamData = $this->sofaScoreRepository->fetchPlayersOfTeam();

            $data = $this->builder->build($squadsData, $playersOfTeamData);
            
            DB::transaction(function () use ($data) {
                $unique = ['id'];
                $updateColumns = ['name', 'number', 'season', 'foot_player_id', 'sofa_player_id'];
                
                PlayerInfo::upsert($data->toArray(), $unique, $updateColumns);
            });

        } catch (Exception $e) {
            throw $e;
        }
    }
}