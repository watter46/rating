<?php declare(strict_types=1);

namespace Database\Stubs\Player;

use Exception;
use Illuminate\Support\Facades\DB;

use App\Models\PlayerInfo;
use App\UseCases\Admin\Player\UpdatePlayerInfosDataBuilder;
use Database\Stubs\Infrastructure\ApiFootball\MockApiFootballRepository;
use Database\Stubs\Infrastructure\SofaScore\MockSofaScoreRepository;


class StubUpdatePlayerInfosUseCase
{
    public function __construct(private UpdatePlayerInfosDataBuilder $builder)
    {
        //
    }

    public function execute(): void
    {
        try {
            $apiFootballRepository = app(MockApiFootballRepository::class);
            $sofaScoreRepository   = app(MockSofaScoreRepository::class);
            
            $squadsData = $apiFootballRepository->fetchSquads();
            $playersOfTeamData = $sofaScoreRepository->fetchPlayersOfTeam();

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