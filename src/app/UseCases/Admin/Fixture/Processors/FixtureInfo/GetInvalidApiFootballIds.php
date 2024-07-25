<?php declare(strict_types=1);

namespace App\UseCases\Admin\Fixture\Processors\FixtureInfo;

use Illuminate\Support\Collection;

use App\Models\PlayerInfo;
use App\UseCases\Util\PlayerName;


readonly class GetInvalidApiFootballIds
{
    /**
     * flash_live_sports_idがnull
     * または登録されていないapi_football_idを取得する
     *
     * @param  Collection<PlayerInfo> $playerInfos
     * @param  Collection $players
     * @return Collection<int>
     */
    public function execute(Collection $playerInfos, Collection $players): Collection
    {
        return collect()
            ->merge($this->filterNullFlashLiveSportsIdPlayers($playerInfos))
            ->merge($this->filterUnregisteredPlayers($playerInfos, $players))
            ->merge($this->filterShortenNamePlayers($playerInfos, $players));

        // return $invalidIds
        //     ->map(function (int $apiFootballId) use ($playerInfos) {
        //         /** @var PlayerInfo $playerInfo */
        //         $playerInfo = $playerInfos
        //             ->first(fn(PlayerInfo $playerInfo) =>
        //                 $playerInfo->api_football_id === $apiFootballId
        //             );

        //         return [
        //             'player_info_id'  => $playerInfo?->id,
        //             'api_football_id' => $apiFootballId
        //         ];
        //     });
    }

    /**
     * flash_live_sports_idがNullのapi_football_idを取得する
     *
     * @param  Collection<int, PlayerInfo> $playerInfos
     * @return Collection<int>
     */
    private function filterNullFlashLiveSportsIdPlayers(Collection $playerInfos): Collection
    {
        return $playerInfos->whereNull('flash_live_sports_id')->pluck('api_football_id');
    }

    /**
     * まだ保存されていないプレイヤーのapi_football_idを取得する
     *
     * @param  Collection<PlayerInfo> $playerInfos
     * @param  Collection $players
     * @return Collection<int>
     */
    private function filterUnregisteredPlayers(Collection $playerInfos, Collection $players): Collection
    {
        return $players->pluck('id')->diff($playerInfos->pluck('api_football_id'));
    }
    
    /**
     * 名前が省略された形式("FirstName. LastName")で保存されているapi_football_idを取得する
     *
     * @param  Collection<PlayerInfo> $playerInfos
     * @return Collection<int>
     */
    private function filterShortenNamePlayers(Collection $playerInfos): Collection
    {
        return $playerInfos
            ->filter(function (PlayerInfo $playerInfo) {
                return PlayerName::create($playerInfo->name)->isShorten();
            })
            ->pluck('api_football_id');
    }
}