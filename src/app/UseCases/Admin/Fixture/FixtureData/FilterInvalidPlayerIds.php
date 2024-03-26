<?php declare(strict_types=1);

namespace App\UseCases\Admin\Fixture\FixtureData;

use Illuminate\Support\Collection;

use App\Models\PlayerInfo;


/**
 * プレーした選手でDBに保存されていないIDを取得する
 */
readonly class FilterInvalidPlayerIds
{
    /**
     * sofa_player_idがnull
     * または登録されていないプレイヤーを取得する
     *
     * @param  Collection $playedPlayerIds
     * @return Collection<Collection<model: PlayerInfo, player: array>>
     */
    public function execute(Collection $playedPlayerIds): Collection
    {
        /** @var Collection<int, PlayerInfo> $playerInfos */
        $playerInfos = PlayerInfo::query()
            ->currentSeason()
            ->whereIn('foot_player_id', $playedPlayerIds)
            ->get();

        $nullSofaIds = $this->filterNullSofaIds($playerInfos);
        $unregisteredPlayers = $this->filterUnregisteredPlayers($playerInfos, $playedPlayerIds);

        return $nullSofaIds->merge($unregisteredPlayers);
    }
    
    /**
     * sofa_player_idがNullのfoot_player_idを取得する
     *
     * @param  Collection<int, PlayerInfo> $playerInfos
     * @return Collection<int>
     */
    private function filterNullSofaIds(Collection $playerInfos): Collection
    {
        return $playerInfos
            ->whereNull('sofa_player_id')
            ->pluck('foot_player_id');
    }

    /**
     * まだ保存されていないプレイヤーのfoot_player_idを取得する
     *
     * @param  Collection<int, PlayerInfo> $playerInfos
     * @param  Collection<int> $footPlayerIdList
     * @return Collection<int>
     */
    private function filterUnregisteredPlayers(Collection $playerInfos, Collection $footPlayerIdList): Collection
    {
        return $footPlayerIdList->diff($playerInfos->pluck('foot_player_id'));
    }
}