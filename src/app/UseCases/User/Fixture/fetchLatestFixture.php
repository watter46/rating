<?php declare(strict_types=1);

namespace App\UseCases\User\Fixture;

use App\Models\Average;
use Exception;

use App\Models\Fixture;
use App\Models\FixtureInfo;
use App\Models\Player;
use App\Models\PlayerInfo;


final readonly class fetchLatestFixture
{
    public function execute(): Fixture
    {
        try {
            $fixtureInfo = FixtureInfo::query()
                ->select('id')
                ->currentSeason()
                ->inSeasonTournament()
                ->finished()
                ->untilToday()
                ->first();
            
            /** @var Fixture $fixture */
            $fixture = Fixture::query()
                ->with([
                    FixtureInfo::SELECT_COLUMNS => [PlayerInfo::SELECT_COLUMNS],
                    'players'
                ])
                ->selectWithout()
                ->fixtureInfoId($fixtureInfo->id)
                ->firstOrNew(['fixture_info_id' => $fixtureInfo->id]);
            
            $averages = Average::query()
                ->select(['player_info_id', 'rating', 'mom'])
                ->fixtureInfoId($fixtureInfo->id)
                ->get()
                ->keyBy('player_info_id');
                
            /** @var Collection $notInPlayerInfoIds */
            $notInPlayerInfoIds = $fixture->fixtureInfo->playerInfos->pluck('id')
                ->diff($fixture->players->pluck('player_info_id'));
            
            $fixtureDomain = $fixture->toDomain();
                
            $fixture->players
                ->push(
                    ...$notInPlayerInfoIds
                        ->map(function (string $playerInfoId) {
                            return new Player(['player_info_id' => $playerInfoId]);
                        }
                ))
                ->map(function (Player $player) use ($fixtureDomain, $averages) {
                    return $player
                        ->setAttribute('average', $averages->get($player->player_info_id))
                        ->setAttribute('canRate', $fixtureDomain->canRate($player))
                        ->setAttribute('canMom', $fixtureDomain->canMom($player))
                        ->setAttribute('rateLimit', $fixtureDomain->getRateCountLimit());
                });

            $fixture->momLimit = $fixtureDomain->getMomCountLimit();

            return $fixture;

        } catch (Exception $e) {
            throw $e;
        }
    }
}