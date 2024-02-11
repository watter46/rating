<?php declare(strict_types=1);

namespace App\UseCases\Player;

use Illuminate\Support\Collection;

use App\Models\Fixture;
use App\Models\PlayerInfo;


readonly class FilterUnregisteredPlayers
{
    public function __construct()
    {
        
    }

    /**
     * sofa_player_idがnull
     * または登録されていないプレイヤーを取得する
     *
     * @param  Fixture $model
     * @return Collection<Collection<model: PlayerInfo, player: array>>
     */
    public function execute(Fixture $model): Collection
    {
        $players = $model->fixture->get('lineups')->flatten(2);

        $lineupIds = $players->pluck('id');
                
        /** @var Collection<int, PlayerInfo> $lineupPlayerInfos */
        $lineupPlayerInfos = PlayerInfo::query()
            ->currentSeason()
            ->whereIn('foot_player_id', $lineupIds)
            ->get();

        $nullSofaIds = $this->filterNullSofaIds($lineupPlayerInfos);
        $unregisteredPlayers = $this->filterUnregisteredPlayers($lineupPlayerInfos, $lineupIds);

        $footPlayerIds = $nullSofaIds->merge($unregisteredPlayers);

        return $footPlayerIds
            ->map(function (int $footPlayerId) use ($lineupPlayerInfos, $players) {
                return collect([
                    'model'  => $lineupPlayerInfos->where('foot_player_id', $footPlayerId),
                    'player' => $players->where('id', $footPlayerId)->first()
                ]);
            });
    }
    
    /**
     * sofa_player_idがNullのfoot_player_idを取得する
     *
     * @param  Collection<int, PlayerInfo> $lineupPlayerInfos
     * @return Collection<int>
     */
    private function filterNullSofaIds(Collection $lineupPlayerInfos): Collection
    {
        return $lineupPlayerInfos
            ->whereNull('sofa_player_id')
            ->pluck('foot_player_id');
    }

    /**
     * まだ保存されていないプレイヤーのfoot_player_idを取得する
     *
     * @param  Collection<int, PlayerInfo> $lineupPlayerInfos
     * @param  Collection<int> $footPlayerIdList
     * @return Collection<int>
     */
    private function filterUnregisteredPlayers(Collection $lineupPlayerInfos, Collection $footPlayerIdList): Collection
    {
        return $footPlayerIdList->diff($lineupPlayerInfos->pluck('foot_player_id'));
    }
}