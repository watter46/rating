<?php declare(strict_types=1);

namespace App\UseCases\Admin\Fixture\FixtureInfoData;

use Illuminate\Support\Collection;

use App\Models\PlayerInfo;


/**
 * プレーした選手でDBに保存されていないFootPlayerIDを取得する
 */
readonly class FilterInvalidFootPlayerIds
{
    /**
     * sofa_player_idがnull
     * または登録されていないfoot_player_idを取得する
     *
     * @param  Collection $playedFootPlayerIds
     * @return Collection<int>
     */
    public function execute(Collection $playedFootPlayerIds): Collection
    {
        /** @var Collection<int, PlayerInfo> $playerInfos */
        $playerInfos = PlayerInfo::query()
            ->currentSeason()
            ->whereIn('foot_player_id', $playedFootPlayerIds)
            ->get();

        $nullSofaIds = $this->filterNullSofaIds($playerInfos);
        $unregisteredPlayerIds = $this->filterUnregisteredPlayerIds($playerInfos, $playedFootPlayerIds);

        return $nullSofaIds->merge($unregisteredPlayerIds);
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
    private function filterUnregisteredPlayerIds(Collection $playerInfos, Collection $footPlayerIdList): Collection
    {
        return $footPlayerIdList->diff($playerInfos->pluck('foot_player_id'));
    }
}