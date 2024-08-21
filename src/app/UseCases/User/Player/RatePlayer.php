<?php declare(strict_types=1);

namespace App\UseCases\User\Player;

use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\ModelNotFoundException;

use App\Models\Fixture;
use App\Models\Player;


final readonly class RatePlayer
{    
    public function execute(string $fixtureInfoId, string $playerInfoId, float $rating)
    {
        try {
            /** @var Fixture $fixture */
            $fixture = Fixture::query()
                ->with(['players' => fn ($query) => $query
                    ->where('player_info_id', $playerInfoId)
                ])
                ->byFixtureInfoId($fixtureInfoId)
                ->selectWithout()
                ->firstOrNew(['fixture_info_id' => $fixtureInfoId]);

            $player = $fixture->players->first()
                ?? new Player([
                    'player_info_id' => $playerInfoId,
                    'fixture_id' => $fixture?->id
                ]);

            $fixtureDomain = $fixture->toDomain();
                
            $player->rate($rating);
            
            if ($fixtureDomain->exceedPeriodDay()) {
                throw new Exception($fixtureDomain::RATE_PERIOD_EXPIRED_MESSAGE);
            }

            if ($fixtureDomain->exceedRateLimit($player)) {
                throw new Exception($fixtureDomain::RATE_LIMIT_EXCEEDED_MESSAGE);
            }
            
            DB::transaction(function () use ($fixture, $player) {
                if (!$fixture->id) {
                    $fixture->save();

                    $player->fixture()->associate($fixture->refresh());
                    $player->save();

                    return;
                }

                $player->save();
            });

            return $fixtureDomain->make($player);

        } catch (ModelNotFoundException $e) {
            throw new ModelNotFoundException('Player Not Found');

        } catch (Exception $e) {
            // dd($e);
            throw $e;
        }
    }
}