<?php declare(strict_types=1);

namespace App\UseCases\Player;

use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\ModelNotFoundException;

use App\Models\Fixture;
use App\Models\Player;
use Illuminate\Support\Facades\Log;

final readonly class EvaluatePlayerUseCase
{
    public function __construct(private Player $player, private Fixture $fixture)
    {
        //
    }

    public function execute(string $fixtureId, string $playerInfoId, float $rating): Player
    {
        try {
            if (!$this->fixture->canEvaluate($fixtureId)) {
                throw new Exception(Fixture::EVALUATION_PERIOD_EXPIRED_MESSAGE);
            }

            /** @var Player $player */
            $player = Player::query()
                ->fixture($fixtureId)
                ->playerInfo($playerInfoId)
                ->first()
                ?? $this->player->associatePlayer($fixtureId, $playerInfoId);

            $player->evaluate($rating);

            Log::info($player->rating);

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