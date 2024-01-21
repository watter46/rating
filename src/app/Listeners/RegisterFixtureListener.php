<?php declare(strict_types=1);

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Str;

use App\Events\FixtureRegistered;
use App\Models\PlayerInfo;
use App\Models\Fixture;
use App\UseCases\Player\RegisterPlayerOfTeamUseCase;
use App\UseCases\Player\RegisterPlayerUseCase;
use App\UseCases\Player\Util\SofaScore;
use Exception;
use Illuminate\Support\Collection;

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
            dd($e);
        }
    }

    private function filterPlayers(Fixture $model): Collection
    {
        $playerIdList = $this->playerIdList($model->fixture['lineups']);

        $playerInfos = PlayerInfo::query()
            ->currentSeason()
            ->whereIn('foot_player_id', $playerIdList)
            ->get();
        
        $modelIdList = PlayerInfo::query()
            ->currentSeason()
            ->whereIn('foot_player_id', $playerIdList)
            ->pluck('foot_player_id');

        return collect($model->fixture['lineups'])
            ->map(function ($lineup, $key) {                
                if ($key === 'startXI') {
                    return collect($lineup)
                        ->flatten(1)
                        ->map(function ($player) {
                            return $player;
                        });
                }

                return collect($lineup)->map(fn ($player) => $player);
            })
            ->flatten(1)
            ->whereIn('id', $playerIdList->diff($modelIdList))
            ->values();
    }

    private function playerIdList(array $lineup): Collection
    {
        return collect($lineup)
            ->map(function ($lineup, $key) {                
                if ($key === 'startXI') {
                    return collect($lineup)
                        ->flatten(1)
                        ->map(function ($player) {
                            return $player['id'];
                        });
                }

                return collect($lineup)->map(fn ($player) => $player['id']);
            })
            ->flatten();
    }
}