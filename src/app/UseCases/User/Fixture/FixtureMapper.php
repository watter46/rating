<?php declare(strict_types=1);

namespace App\UseCases\User\Fixture;

use App\Models\Fixture as ModelsFixture;
use Illuminate\Support\Collection;

use App\UseCases\User\Accessors\Fixture;


class FixtureMapper
{
    public function __construct(ModelsFixture $fixture)
    {
        
    }
    
    public function map(Fixture $fixture): Collection
    {        
        return collect([
            'mom_count' => $fixture->getMomCount(),
            'mom_limit' => $fixture->getMomLimit(),
            'fixture_id' => $fixture->getFixtureId(),
            'fixture_info_id' => $fixture->getFixtureInfoId(),
            'fixture_info' => $this->mapFixtureInfo()
        ]);
    }

    private function mapFixtureInfo()
    {
        $playersByPlayerInfoId = $fixture
            ->dataGet('players')
            ->keyBy('player_info_id')
            ->map(fn (Collection $player) => $player->except(['player_info_id', 'fixture_id']));

        $playerInfosByPlayerId = $fixture
            ->dataGet('fixture_info.player_infos')
            ->map(function (Collection $playerInfo) use ($playersByPlayerInfoId) {
                $player = $playersByPlayerInfoId->get($playerInfo['id'])
                    ?? [
                        'rating' => null,
                        'mom' => false,
                        'rate_count' => 0
                    ];
                
                return $playerInfo->merge($player);
            })
            ->keyBy('api_player_id');
            
        $lineups = $fixture
            ->dataGet('fixture_info.lineups')
            ->map(fn (Collection $lineup) => $lineup
                ->map(function (Collection $player) use ($playerInfosByPlayerId) {
                    $playerInfo = $playerInfosByPlayerId->get($player['id']);
                    dd($fixture);
                    collect([
                        'fixture_info_id' => $playerInfo->dataGet('users_player_rating.fixture_info_id', false),
                        'player_info_id' => $playerInfo->dataGet('users_player_rating.player_info_id', false),
                        'canRate' => $player->canRate,
                        'canMom' => $player->canMom,
                        'momCount' => $fixture->getMomCount(),
                        'momLimit' => $fixture->getMomLimit(),
                        'rateCount' => $playerInfo['rate_count'],
                        'rateLimit' => $player->rateLimit,
                        'goals' => $player['goal'],
                        'grid' => $player['grid'],
                        'name' => $player['name'],
                        'number' => $player['number'],
                        'assists' => $player['assists'],
                        'position' => $player['position'],
                        'ratings' => [
                            'my' => [
                                'rating' => $playerInfo['rating'],
                                'mom' => $playerInfo['mom']
                            ],
                            'users' => [
                                'rating' => $playerInfo->dataGet('users_player_rating.rating', false),
                                'mom' => $playerInfo->dataGet('users_player_rating.mom', false)
                            ],
                            'machine' => $player['rating']
                        ]
                    ]);
                })
            );
            
        // return $fixtureInfo
        //     ->except('id')
        //     ->put('player_infos', $playerInfos);
    }
}