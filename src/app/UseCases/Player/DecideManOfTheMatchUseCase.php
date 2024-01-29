<?php declare(strict_types=1);

namespace App\UseCases\Player;

use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\ModelNotFoundException;

use App\Models\Fixture;
use App\Models\Player;


final readonly class DecideManOfTheMatchUseCase
{
    public function __construct(private Fixture $fixture, private Player $player)
    {
        //
    }

    public function execute(string $fixtureId, string $playerInfoId): Player
    {
        try {            
            if (!$this->fixture->canEvaluate($fixtureId)) {
                throw new Exception(Fixture::EVALUATION_PERIOD_EXPIRED_MESSAGE);
            }
            
            /** @var Player $player */
            $player = Player::query()
                ->fixture($fixtureId)
                ->playerInfo($playerInfoId)
                ->first();
                        
            if ($player?->mom) {
                return $player;
            }

            $newMomPlayer = $player 
                ? $player->decideMOM()
                : $this->player
                    ->associatePlayer($fixtureId, $playerInfoId)
                    ->decideMOM();

            /** @var Player $oldMomPlayer */
            $oldMomPlayer = Player::query()
                ->mom($fixtureId)
                ?->first()
                ?->unDecideMOM();

            DB::transaction(function () use ($newMomPlayer, $oldMomPlayer) {
                $newMomPlayer->save();

                if (!$oldMomPlayer) return;
                
                $oldMomPlayer->save();
            });

            return $newMomPlayer->refresh()->evaluated();

        } catch (ModelNotFoundException $e) {
            throw new ModelNotFoundException('Player Not Found');

        } catch (Exception $e) {
            throw $e;
        }
    }
}