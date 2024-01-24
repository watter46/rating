<?php declare(strict_types=1);

namespace App\UseCases\Player;

use App\Models\Player;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\ModelNotFoundException;


final readonly class DecideManOfTheMatchUseCase
{
    public function __construct()
    {
        //
    }

    public function execute(string $fixtureId, string $playerInfoId): void
    {
        try {            
            /** @var Player $player */
            $player = Player::query()
                ->fixture($fixtureId)
                ->playerInfo($playerInfoId)
                ->first();
                        
            if ($player?->mom) return;

            $newMomPlayer = $player 
                ? $player->decideMOM()
                : (new Player)
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

        } catch (ModelNotFoundException $e) {
            throw new ModelNotFoundException('Player Not Found');

        } catch (Exception $e) {
            throw $e;
        }
    }
}