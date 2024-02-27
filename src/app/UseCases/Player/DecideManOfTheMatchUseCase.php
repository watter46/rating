<?php declare(strict_types=1);

namespace App\UseCases\Player;

use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Collection;

use App\Models\Fixture;
use App\Models\Player;


final readonly class DecideManOfTheMatchUseCase
{
    public function __construct(private Fixture $fixture, private Player $player)
    {
        //
    }
    
    /**
     * execute
     *
     * @param  string $fixtureId
     * @param  string $playerInfoId
     * @return Collection<int, string>
     */
    public function execute(string $fixtureId, string $playerInfoId): Collection
    {
        try {            
            if (!$this->fixture->canRate($fixtureId)) {
                throw new Exception(Fixture::RATE_PERIOD_EXPIRED_MESSAGE);
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

            return collect([
                'newMomId' => $newMomPlayer->player_info_id,
                'oldMomId' => $oldMomPlayer->player_info_id,
            ]);

        } catch (ModelNotFoundException $e) {
            throw new ModelNotFoundException('Player Not Found');

        } catch (Exception $e) {
            throw $e;
        }
    }
}