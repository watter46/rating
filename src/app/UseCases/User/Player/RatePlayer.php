<?php declare(strict_types=1);

namespace App\UseCases\User\Player;

use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\ModelNotFoundException;

use App\UseCases\User\FixtureRequest;


final readonly class RatePlayer
{    
    public function execute(FixtureRequest $request, float $rating)
    {
        try {
            $builder = $request->buildFixture()->assignPlayer($request);
            
            $fixture = $builder->get();
            $player = $builder->getPlayer();

            if ($builder->exceedPeriodDay()) {
                throw new Exception($builder->validator()::RATE_PERIOD_EXPIRED_MESSAGE);
            } 

            if ($builder->exceedRateLimit()) {
                throw new Exception($builder->validator()::RATE_LIMIT_EXCEEDED_MESSAGE);
            }
            
            $player->rate($rating);
            
            DB::transaction(function () use ($fixture, $player) {
                if (!$fixture->id) {
                    $fixture->save();

                    $player->fixture()->associate($fixture->refresh());
                    $player->save();

                    return;
                }

                $player->save();
            });

            return $builder
                ->assignUpdatedPlayer($player)
                ->addColumnValidationToPlayer()
                ->getPlayer();

        } catch (ModelNotFoundException $e) {
            throw new ModelNotFoundException('Player Not Found');

        } catch (Exception $e) {
            throw $e;
        }
    }
}