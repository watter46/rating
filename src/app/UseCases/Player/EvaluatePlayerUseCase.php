<?php declare(strict_types=1);

namespace App\UseCases\Player;

use App\Models\PlayerInfo;
use App\Models\Fixture;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\ModelNotFoundException;

use App\Models\Player;


final readonly class EvaluatePlayerUseCase
{
    public function __construct(private Player $player, private Fixture $fixture, private PlayerInfo $playerInfo)
    {
        //
    }

    public function execute(string $fixtureId, string $playerId, float $rating): Player
    {
        try {
            $player = Player::query()
                ->where('fixture_id', $fixtureId)
                ->where('player_info_id', $playerId)
                ->first();
            
            /** @var Player $model */
            $model = $player ?? $this->player->associatePlayer($fixtureId, $playerId);

            $player = $model->evaluate($rating);

            DB::transaction(function () use ($player) {
                $player->save();
            });

            return $player;

        } catch (ModelNotFoundException $e) {
            throw new ModelNotFoundException('Playerが見つかりませんでした。');

        } catch (Exception $e) {
            throw $e;
        }
    }
}