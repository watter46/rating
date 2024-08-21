<?php declare(strict_types=1);

namespace App\UseCases\Admin\UsersPlayerRating\Accessors;

use Illuminate\Support\Collection;

use App\Models\Fixture as FixtureModel;


class UsersPlayerRating
{
    public function upsert(string $fixtureInfoId)
    {
        $fixtures = FixtureModel::query()
            ->select(['id', 'fixture_info_id'])
            ->with([
                'fixtureInfo:id' => ['playerInfos:id'],
                'players:fixture_id,rating,mom,player_info_id'
            ])
            ->byFixtureInfoId($fixtureInfoId)
            ->get()
            ->toCollection();
        
        if ($fixtures->isEmpty()) return;

        $usersRatingsById = $this->calculateRatings($fixtures)->keyBy('player_info_id');
        
        return $this->getUsersRatings($fixtures)
            ->map(function (Collection $Rating) use ($usersRatingsById) {
                $newRating = $usersRatingsById->get($Rating['player_info_id']);

                if (!$newRating) {
                    return $Rating;
                }

                return [
                    'id'              => $Rating['id'],
                    'player_info_id'  => $Rating['player_info_id'],
                    'fixture_info_id' => $Rating['fixture_info_id'],
                    'rating'          => $newRating['rating'],
                    'mom'             => $newRating['mom'],
                ];
            })
            ->toArray();
    }

    private function getUsersRatings(Collection $fixtures)
    {        
        return $fixtures
            ->first()
            ->dataGet('fixture_info.player_infos')
            ->pluck('users_player_rating');
    }
    
    /**
     * 評価されている選手のみのレーティングを取得する
     *
     * @param  Collection $fixtures
     * @return Collection
     */
    private function calculateRatings(Collection $fixtures): Collection
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