<?php declare(strict_types=1);

namespace App\UseCases\User\Player;

use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\ModelNotFoundException;

use App\Models\Player;
use App\UseCases\User\FixtureRequest;


final readonly class DecideManOfTheMatch
{    
    /**
     * execute
     *
     * @param  mixed $request
     * @return array{newMomPlayer: Player, oldMomPlayer: Player}
     */
    public function execute(FixtureRequest $request)
    {
        try {
            $builder = $request->buildFixture()->assignPlayer($request);

            $fixture = $builder->get();
            $player  = $builder->getPlayer();

            if ($builder->exceedPeriodDay()) {
                throw new Exception($builder->validator()::RATE_PERIOD_EXPIRED_MESSAGE);
            } 

            if ($builder->exceedRateLimit()) {
                throw new Exception($builder->validator()::RATE_LIMIT_EXCEEDED_MESSAGE);
            }

            /** @var Player $oldMomPlayer */
            $oldMomPlayer = $fixture
                ->players
                ->first(fn(Player $player) => $player->mom)
                ?->unDecideMOM();
            
            $newMomPlayer = $player->decideMOM();

            $fixture->incrementMomCount();

            DB::transaction(function () use ($fixture, $oldMomPlayer, $newMomPlayer) {
                $fixture->save();

                $newMomPlayer->fixture()->associate($fixture->id ?? $fixture->refresh());
                $newMomPlayer->save();

                if (!$oldMomPlayer) return;

                $oldMomPlayer->save();
            });

            return [
                'newMomPlayer' => $builder
                    ->assignUpdatedPlayer($newMomPlayer)
                    ->addColumnValidationToPlayer()
                    ->getPlayer(),
                'oldMomPlayer' => $oldMomPlayer
                    ? $builder
                        ->assignUpdatedPlayer($oldMomPlayer)
                        ->addColumnValidationToPlayer()
                        ->getPlayer()
                    : null
                ];

        } catch (ModelNotFoundException $e) {
            throw new ModelNotFoundException('Player Not Found');

        } catch (Exception $e) {
            throw $e;
        }
    }
}