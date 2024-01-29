<?php declare(strict_types=1);

namespace App\UseCases\Player;

use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\ModelNotFoundException;

use App\Models\Fixture;
use App\Models\Player;


final readonly class EvaluatePlayerUseCase
{
    public function __construct(private Player $player, private Fixture $fixture)
    {
        //
    }

    public function execute(string $fixtureId, string $playerId, float $rating): Player
    {
        try {
            if (!$this->fixture->canEvaluate($fixtureId)) {
                throw new Exception(Fixture::EVALUATION_PERIOD_EXPIRED_MESSAGE);
            }

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
            
            // Attributeにするか検討する
            return $player->refresh()->evaluated();

        } catch (ModelNotFoundException $e) {
            throw new ModelNotFoundException('Player Not Found');

        } catch (Exception $e) {
            dd($e);
            throw $e;
        }
    }
}