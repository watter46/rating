<?php declare(strict_types=1);

namespace App\UseCases\Player;

use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

use App\Models\Fixture;
use App\Models\Player;
use App\UseCases\Player\Builder\RateAllPlayerDataBuilder;


final readonly class RateAllPlayersUseCase
{
    public function __construct(private Fixture $fixture, private RateAllPlayerDataBuilder $builder)
    {
        //
    }

    public function execute(string $fixtureId, Collection $ratedPlayers)
    {
        try {
            if (!$this->fixture->canEvaluate($fixtureId)) {
                throw new Exception(Fixture::EVALUATION_PERIOD_EXPIRED_MESSAGE);
            }
            
            $players = Player::query()
                ->fixture($fixtureId)
                ->get();

            $data = $this->builder->build($players, $ratedPlayers, $fixtureId);
            
            DB::transaction(function () use ($data) {
                $unique = ['id'];
                $updateColumns = ['mom', 'rating'];
                
                Player::upsert($data, $unique, $updateColumns);
            });

        } catch (Exception $e) {
            throw $e;
        }
    }
}