<?php declare(strict_types=1);

namespace App\UseCases\User\Fixture;

use Exception;
use Illuminate\Support\Collection;

use App\Models\Average;
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
                ->with([
                    FixtureInfo::SELECT_COLUMNS => [PlayerInfo::SELECT_COLUMNS],
                    'players'
                ])
                ->selectWithout()
                ->fixtureInfoId($fixtureInfoId)
                ->firstOrNew(['fixture_info_id' => $fixtureInfoId]);
            
            $averages = Average::query()
                ->select(['player_info_id', 'rating', 'mom'])
                ->fixtureInfoId($fixtureInfoId)
                ->get()
                ->keyBy('player_info_id');
                
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
                ->map(function (Player $player) use ($fixtureDomain, $averages) {
                    return $fixtureDomain
                        ->make($player)
                        ->setAttribute('average', $averages->get($player->player_info_id));
                });

            $fixture->momLimit = $fixtureDomain->getMomCountLimit();

            return $fixture;

        } catch (Exception $e) {
            throw $e;
        }
    }
}