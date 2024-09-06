<?php declare(strict_types=1);

namespace App\UseCases\User\Fixture;

use Exception;

use App\UseCases\User\Domain\FixtureInfoId;
use App\UseCases\User\Fixture\FixtureQueryServiceInterface;


final readonly class FindFixture
{
    public function __construct(private FixtureQueryServiceInterface $query)
    {
        //
    }
    
    public function execute(string $fixtureInfoId)
    {
        try {
            return $this->query->find(FixtureInfoId::reconstruct($fixtureInfoId));
            
            // $playerInfosById = $fixture->fixtureInfo->playerInfos
            //     ->keyBy('id')
            //     ->map(function (PlayerInfo $playerInfo) {
            //         return [
            //             'rating' => $playerInfo->users_player_rating->rating,
            //             'mom' => $playerInfo->users_player_rating->mom
            //         ];
            //     });
                
            // /** @var Collection $notInPlayerInfoIds */
            // $notInPlayerInfoIds = $fixture
            //     ->fixtureInfo
            //     ->playerInfos
            //     ->pluck('id')
            //     ->diff($fixture->players->pluck('player_info_id'));
            
            // $fixtureDomain = $fixture->toDomain();
                
            // $fixture->players
            //     ->push(
            //         ...$notInPlayerInfoIds
            //             ->map(function (string $playerInfoId) {
            //                 return new Player(['player_info_id' => $playerInfoId]);
            //             }
            //     ))
            //     ->map(function (Player $player) use ($fixtureDomain, $playerInfosById) {
            //         return $fixtureDomain
            //             ->make($player)
            //             ->setAttribute('usersRating', $playerInfosById->get($player->player_info_id));
            //     });

            // $fixture->momLimit = $fixtureDomain->getMomCountLimit();
            
            // return $fixture;

        } catch (Exception $e) {
            throw $e;
        }
    }
}