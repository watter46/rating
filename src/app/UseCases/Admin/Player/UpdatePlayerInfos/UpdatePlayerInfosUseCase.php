<?php declare(strict_types=1);

namespace App\UseCases\Admin\Player\UpdatePlayerInfos;

use App\Models\PlayerInfo;
use App\UseCases\Admin\Player\UpdatePlayerInfos\SquadsData;
use App\UseCases\Api\ApiFootball\SquadsFetcher;
use App\UseCases\Api\SofaScore\PlayersOfTeamFetcher;
use Exception;
use Illuminate\Support\Facades\DB;


final readonly class UpdatePlayerInfosUseCase
{
    public function __construct(
        private SquadsFetcher $squadsFetcher,
        private PlayersOfTeamFetcher $playersOfTeamFetcher,
        private UpdatePlayerInfosDataBuilder $builder
    )
    {
        //
    }

    public function execute(): void
    {
        try {
            $squadsData = $this->squadsFetcher->fetch();
            $playersOfTeamData = $this->playersOfTeamFetcher->fetch();
                
            $data = $this->builder->build(
                SquadsData::build($squadsData),
                PlayersOfTeamData::build($playersOfTeamData),
                PlayerInfo::currentSeason()->get()
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