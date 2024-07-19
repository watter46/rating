<?php declare(strict_types=1);

namespace App\UseCases\User\Fixture;

use Exception;
use Illuminate\Support\Collection;

use App\Models\Fixture;
use App\Models\FixtureInfo;
use App\Models\Player;
use App\Models\PlayerInfo;


final readonly class FindFixture
{
    public function execute(string $fixtureInfoId): Fixture
    {
        try {
            /** @var Fixture $fixture */
            $fixture = Fixture::query()
                ->selectWithout()
                ->byFixtureInfoId($fixtureInfoId)
                ->firstOrNew(['fixture_info_id' => $fixtureInfoId])
                ->load([
                    FixtureInfo::SELECT_COLUMNS => [PlayerInfo::SELECT_COLUMNS],
                    'players'
                ]);
            
            $playerInfoKeyById = $fixture->fixtureInfo->playerInfos
                ->keyBy('id')
                ->map(function (PlayerInfo $playerInfo) {
                    return [
                        'rating' => $playerInfo->users_player_statistic->rating,
                        'mom' => $playerInfo->users_player_statistic->mom
                    ];
                });
                
            /** @var Collection $notInPlayerInfoIds */
            $notInPlayerInfoIds = $fixture
                ->fixtureInfo
                ->playerInfos
                ->pluck('id')
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