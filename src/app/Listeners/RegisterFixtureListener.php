<?php declare(strict_types=1);

namespace App\Listeners;

use Exception;
use Illuminate\Support\Collection;

use App\Events\FixtureRegistered;
use App\Models\PlayerInfo;
use App\Models\Fixture;
use App\UseCases\Player\RegisterPlayerUseCase;


class RegisterFixtureListener
{
    /**
     * Create the event listener.
     */
    public function __construct(private RegisterPlayerUseCase $registerPlayer)
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(FixtureRegistered $event): void
    {
        try {
            $players = $this->filterPlayers($event->model);
            
            if ($players->isEmpty()) return;

            $this->registerPlayer->execute($players);
            
        } catch (Exception $e) {
            throw $e;
        }
    }
    
    /**
     * sofa_player_idがnull
     * または登録されていないプレイヤーを取得する
     *
     * @param  Fixture $model
     * @return Collection<Collection<model: PlayerInfo, player: array>>
     */
    private function filterPlayers(Fixture $model): Collection
    {
        $players = $this->flatLineups($model->fixture->get('lineups'));
        
        $footPlayerIdList = $players->pluck('id');
                
        /** @var Collection<int, PlayerInfo> $playedPlayerInfos */
        $playedPlayerInfos = PlayerInfo::query()
            ->currentSeason()
            ->whereIn('foot_player_id', $footPlayerIdList)
            ->get();

        $nullSofaPlayerIdList   = $this->filterNullSofaPlayerId($playedPlayerInfos);
        $unregisteredPlayerList = $this->filterUnregisteredPlayers($playedPlayerInfos, $footPlayerIdList);

        $registerFootPlayerIdList = $nullSofaPlayerIdList->merge($unregisteredPlayerList);

        return $registerFootPlayerIdList
            ->map(function (int $footPlayerId) use ($playedPlayerInfos, $players) {
                return collect([
                    'model'  => $playedPlayerInfos->where('foot_player_id', $footPlayerId),
                    'player' => $players->where('id', $footPlayerId)->first()
                ]);
            });
    }
    
    /**
     * sofa_player_idがNullのfoot_player_idを取得する
     *
     * @param  Collection<int, PlayerInfo> $playedPlayerInfos
     * @return Collection<int>
     */
    private function filterNullSofaPlayerId(Collection $playedPlayerInfos): Collection
    {
        return $playedPlayerInfos
            ->whereNull('sofa_player_id')
            ->pluck('foot_player_id');
    }

    /**
     * まだ保存されていないプレイヤーのfoot_player_idを取得する
     *
     * @param  Collection<int, PlayerInfo> $playedPlayerInfos
     * @param  Collection<int> $footPlayerIdList
     * @return Collection<int>
     */
    private function filterUnregisteredPlayers(Collection $playedPlayerInfos, Collection $footPlayerIdList): Collection
    {
        return $footPlayerIdList->diff($playedPlayerInfos->pluck('foot_player_id'));
    }

    private function flatLineups(Collection $lineups): Collection
    {        
        return collect($lineups)
            ->map(function (Collection $lineup, string $key) {
                if ($key === 'startXI') {
                    return $lineup->collapse();
                }
                
                return $lineup;
            })
            ->collapse();
    }
}