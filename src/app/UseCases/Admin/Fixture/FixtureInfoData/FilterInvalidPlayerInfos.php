<?php declare(strict_types=1);

namespace App\UseCases\Admin\Fixture\FixtureInfoData;

use Illuminate\Support\Collection;

use App\Models\PlayerInfo;


/**
 * プレーした選手でDBに保存されていないFootPlayerIDを取得する
 */
readonly class FilterInvalidPlayerInfos
{
    /**
     * flash_live_sports_idがnull
     * または登録されていないapi_football_idを取得する
     *
     * @param  Collection $playedFootPlayerIds
     * @return Collection
     */
    public function execute(Collection $playedFootPlayerIds): Collection
    {
        /** @var Collection<PlayerInfo> $playerInfos */
        $playerInfos = PlayerInfo::query()
            ->currentSeason()
            ->whereIn('api_football_id', $playedFootPlayerIds)
            ->get();

        $nullFlashLiveSortsIds = $this->filterNullFlashLiveSportsIds($playerInfos);
        $unregisteredApiFootballIds = $this->filterUnregisteredPlayerIds($playerInfos, $playedFootPlayerIds);

        $invalidApiFootballIds = $nullFlashLiveSortsIds->merge($unregisteredApiFootballIds);

        return $invalidApiFootballIds
            ->map(function (int $apiFootballId) use ($playerInfos) {
                /** @var PlayerInfo $playerInfo */
                $playerInfo = $playerInfos
                    ->first(fn(PlayerInfo $playerInfo) =>
                        $playerInfo->api_football_id === $apiFootballId
                    );

                return [
                    'player_info_id'  => $playerInfo?->id,
                    'api_football_id' => $apiFootballId
                ];
            });
    }

    /**
     * flash_live_sports_idがNullのapi_football_idを取得する
     *
     * @param  Collection<int, PlayerInfo> $playerInfos
     * @return Collection<int>
     */
    private function filterNullFlashLiveSportsIds(Collection $playerInfos): Collection
    {
        return $playerInfos->whereNull('flash_live_sports_id')->pluck('api_football_id');
    }

    /**
     * まだ保存されていないプレイヤーのapi_football_idを取得する
     *
     * @param  Collection<PlayerInfo> $playerInfos
     * @param  Collection<int> $footPlayerIds
     * @return Collection<int>
     */
    private function filterUnregisteredPlayerIds(Collection $playerInfos, Collection $footPlayerIds): Collection
    {
        return $footPlayerIds->diff($playerInfos->pluck('api_football_id'));
    }
}