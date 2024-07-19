<?php declare(strict_types=1);

namespace App\UseCases\Admin\Player\Processors\UsersRating;

use Illuminate\Support\Collection;


class UsersRatingBuilder
{
    /**
     * build
     *
     * @param  Collection $fixtures
     * @return Collection
     */
    public function build( Collection $fixtures)
    {
        $usersRatingsKeyByPlayerInfoId = $this->calculateUsersRatings($fixtures)->keyBy('player_info_id');
        
        return $this->getUsersFixtureStatistics($fixtures)
            ->map(function (array $statistic) use ($usersRatingsKeyByPlayerInfoId) {
                $newStatistic = $usersRatingsKeyByPlayerInfoId->get($statistic['player_info_id']);

                if (!$newStatistic) {
                    return $statistic;
                }

                return [
                    'id'              => $statistic['id'],
                    'player_info_id'  => $statistic['player_info_id'],
                    'fixture_info_id' => $statistic['fixture_info_id'],
                    'rating'          => $newStatistic['rating'],
                    'mom'             => $newStatistic['mom'],
                ];
            });
    }

    private function getUsersFixtureStatistics(Collection $fixtures)
    {
        return $fixtures
            ->first()
            ->dataGet('fixture_info.player_infos')
            ->pluck('users_player_statistic');
    }
    
    /**
     * 評価されている選手のみのレーティングを取得する
     *
     * @param  Collection $fixtures
     * @return Collection
     */
    private function calculateUsersRatings(Collection $fixtures): Collection
    {
        return $fixtures
            ->pluck('players')
            ->map(function (Collection $players) {
                return $players
                    ->map(fn(Collection $player) => $player->only(['player_info_id', 'rating', 'mom']))
                    ->filter(fn (Collection $player) => $player['rating']);
            })
            ->flatten(1)  
            ->groupBy('player_info_id')
            ->map(function (Collection $players, string $player_info_id) {
                return collect([
                    'player_info_id' => $player_info_id,
                    'momPercent' => $players->percentage(fn($player) => $player['mom']),
                    'rating' => round($players->avg('rating'), 1)
                ]);
            })
            ->values()
            ->pipe(function (Collection $players) {
                /** MOMの割合が一番高い選手をユーザー評価のMOMとする */
                $momPlayerInfoId = $players
                    ->filter(fn($player) => $player['momPercent'])
                    ->sortByDesc('momPercent')
                    ->first()
                    ?->get('player_info_id');

                return $players
                    ->map(function (Collection $player) use ($momPlayerInfoId) {
                        return [
                            'player_info_id' => $player['player_info_id'],
                            'rating' => $player['rating'],
                            'mom' => $player['player_info_id'] === $momPlayerInfoId
                        ];
                    });
            });
    }
}