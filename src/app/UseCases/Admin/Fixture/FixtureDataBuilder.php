<?php declare(strict_types=1);

namespace App\UseCases\Admin\Fixture;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;

use App\Http\Controllers\Util\LeagueImageFile;
use App\Http\Controllers\Util\PlayerImageFile;
use App\Http\Controllers\Util\TeamImageFile;
use App\UseCases\Fixture\Format\Fixture\Fixture;
use App\UseCases\Fixture\Format\Fixture\League;
use App\UseCases\Fixture\Format\Fixture\Lineups;
use App\UseCases\Fixture\Format\Fixture\Players;
use App\UseCases\Fixture\Format\Fixture\Score;
use App\UseCases\Fixture\Format\Fixture\Teams;


final readonly class FixtureDataBuilder
{
    public function __construct(
        private TeamImageFile $teamImage,
        private PlayerImageFile $playerImage,
        private LeagueImageFile $leagueImage,
        private Fixture $fixture,
        private Teams $teams,
        private League $league,
        private Score $score,
        private Lineups $lineups,
        private Players $players)
    {
        //
    }
    
    /**
     * 必要なデータを抽出して一覧にする
     *
     * @param  Collection $fetched
     * @return Collection
     */
    public function build($fetched): Collection
    {
        if (!$this->fixture->isFinished($fetched->get('fixture'))) {
            return collect();
        }

        $data = $fetched
            ->except(['events', 'goals', 'statistics'])
            ->map(function ($data, $key) {
                return match ($key) {
                    'fixture' => $this->fixture->build($data),
                    'teams'   => $this->teams->build($data),
                    'league'  => $this->league->build($data),
                    'score'   => $this->score->build($data),
                    'lineups' => $this->lineups->build($data),
                    'players' => $this->players->build($data)
                };
            });
            
        $lineups = $data
            ->get('lineups')
            ->map(function ($lineup, $key) use ($data) {
                if ($key === 'startXI') {
                    $idList = $lineup->pluck('id');
                    
                    $startXI = $data
                        ->get('players')
                        ->whereIn('id', $idList)
                        ->map(fn ($p) => collect($p))
                        ->values();

                    return $this->merge($startXI, $lineup)->values();
                }

                $idList = $lineup->pluck('id');

                $substitutes = $data
                    ->get('players')
                    ->whereIn('id', $idList)
                    ->map(fn ($p) => collect($p))                    
                    ->values();

                return $this->merge($substitutes, $lineup)->values();
            });
                
        $result = $data
            ->put('lineups', $lineups)
            ->map(function (Collection $lineups, $key) {            
                if ($key === 'lineups') {
                    return $lineups
                        ->map(function (Collection $lineup, string $key) {
                            if ($key === 'startXI') {
                                $this->substitutes(chunkSize: 3, items: $lineup);
                                return $lineup
                                    ->reverse()
                                    ->groupBy(function ($player) {
                                        return Str::before($player['grid'], ':');
                                    })
                                    ->values();
                            }

                            return $this->substitutes(chunkSize: 3, items: $lineup);
                        });
                }

                return $lineups;
            })
            ->except('players');

        return $result;
    }

    private function substitutes(int $chunkSize, Collection $items): Collection
    {
        return $this->chunk($chunkSize, true, $items, collect());
    }

    private function chunk(int $chunkSize, bool $isBig, Collection $items, Collection $result): Collection
    {
        if ($items->isEmpty()) {
            return $result;
        }

        $takeItem = $items->take($isBig ? $chunkSize : $chunkSize - 1);
        $newItems = $items->diff($takeItem);

        return $this->chunk(
            chunkSize: $chunkSize,
            isBig: !$isBig,
            items: $newItems,
            result: $result->push($takeItem)
        );
    }

    private function merge(Collection $players, Collection $lineup): Collection
    {
        return $players
            ->keyBy('id')
            ->map(function (Collection $player, int $footPlayerId) use ($lineup) {
                if ($player->isEmpty()) {
                    return true;
                }
                
                return $player->merge($lineup->keyBy('id')->get($footPlayerId));
            });
    }
}