<?php declare(strict_types=1);

namespace App\UseCases\User\Player;

use Exception;
use Illuminate\Support\Facades\DB;

use App\Models\Player;
use App\Models\Fixture;


final readonly class DecideManOfTheMatch
{
    /**
     * execute
     *
     * @param  string $fixtureInfoId
     * @param  string $playerInfoId
     * @return array{newMom: Player, oldMom: Player}
     */
    public function execute(string $fixtureInfoId, string $playerInfoId)
    {
        try {
            /** @var Fixture $fixture */
            $fixture = Fixture::query()
                ->with([
                    'players' => fn ($query) => $query
                        ->orWhere('player_info_id', $playerInfoId)
                        ->orWhere('mom', true),
                ])
                ->fixtureInfoId($fixtureInfoId)
                ->selectWithout()
                ->fixtureInfoId($fixtureInfoId)
                ->firstOrNew(['fixture_info_id' => $fixtureInfoId]);

            $newMomId = $fixture->players
                ->first(fn(Player $player) => $player->player_info_id === $playerInfoId)
                ?->id;

            $oldMomId = $fixture->players
                ->first(fn(Player $player) => $player->mom)
                ?->id;

            /** @var Player $newMom */
            $newMom = ($fixture->players
                ->first(fn (Player $player) => $player->id === $newMomId)
                ?? new Player([
                    'player_info_id' => $playerInfoId,
                    'fixture_id' => $fixture?->id
                ]))
                ->decideMOM();

            /** @var Player $oldMom */
            $oldMom = $fixture->players
                ->first(fn (Player $player) => $player->id === $oldMomId)
                ?->unDecideMOM();

            $fixtureDomain = $fixture->toDomain();
                
            $fixture->incrementMomCount();
            
            if ($fixtureDomain->exceedPeriodDay()) {
                throw new Exception($fixtureDomain::RATE_PERIOD_EXPIRED_MESSAGE);
            }

            if ($fixtureDomain->exceedMomLimit()) {
                throw new Exception($fixtureDomain::MOM_LIMIT_EXCEEDED_MESSAGE);
            }

            DB::transaction(function () use ($fixture, $newMom, $oldMom) {
                $fixture->save();

                $newMom->fixture()->associate($fixture->id ?? $fixture->refresh());
                $newMom->save();

                if (!$oldMom) return;

                $oldMom->save();
            });

            return [
                $fixture->toDomain()->make($newMom),
                $fixture->toDomain()->make($oldMom)
            ];

        } catch (Exception $e) {
            throw $e;
        }
    }
}