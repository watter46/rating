<?php declare(strict_types=1);

namespace App\UseCases\User\Fixture;

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
                ->selectWithout()
                ->byFixtureInfoId($fixtureInfo->id)
                ->firstOrNew(['fixture_info_id' => $fixtureInfo->id])
                ->load([
                    FixtureInfo::SELECT_COLUMNS => [PlayerInfo::SELECT_COLUMNS],
                    'players'
                ]);

            $playerInfoKeyById = $fixture->fixtureInfo->playerInfos
                ->keyBy('id')
                ->map(function (PlayerInfo $playerInfo) {
                    return [
                        'rating' => $playerInfo->pivot->rating,
                        'mom' => $playerInfo->pivot->mom
                    ];
                });
                
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
                ->map(function (Player $player) use ($fixtureDomain, $playerInfoKeyById) {
                    return $fixtureDomain
                        ->make($player)
                        ->setAttribute('usersRating', $playerInfoKeyById->get($player->player_info_id));
                });

            $fixture->momLimit = $fixtureDomain->getMomCountLimit();

            return $fixture;

        } catch (Exception $e) {
            throw $e;
        }
    }
}