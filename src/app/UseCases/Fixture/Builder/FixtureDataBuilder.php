<?php declare(strict_types=1);

namespace App\UseCases\Fixture\Builder;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;

use App\Http\Controllers\PositionType;
use App\Http\Controllers\Util\LeagueImageFile;
use App\Http\Controllers\Util\PlayerImageFile;
use App\Http\Controllers\Util\TeamImageFile;


final readonly class FixtureDataBuilder
{
    private const CHELSEA_TEAM_ID = 49;
    private const END_STATUS = 'Match Finished';

    public function __construct(
        private TeamImageFile $teamImage,
        private PlayerImageFile $playerImage,
        private LeagueImageFile $leagueImage)
    {
        
    }
    
    /**
     * 必要なデータを抽出して一覧にする
     *
     * @param  mixed $fetched
     * @return Collection
     */
    public function build($fetched): Collection
    {
        $data = collect($fetched)
            ->except(['events', 'goals', 'statistics'])
            ->map(function ($data, $key) {
                return match ($key) {
                    'fixture' => $this->fixture($data),
                    'teams'   => $this->teams($data),
                    'league'  => $this->league($data),
                    'score'   => $this->score($data),
                    'lineups' => $this->lineups($data),
                    'players' => $this->players($data),
                };
            });

        $players = $data['players'];
        
        $result = $data->transform(function ($lineups, $key) use ($players) {
            if ($key === 'lineups') {       
                $startXI = collect($lineups['startXI'])->pluck('id');
                $startXIRatings = collect($players)->whereIn('id', $startXI);

                $substitutes = collect($lineups['substitutes'])->pluck('id');
                $substituteRatings = collect($players)->whereIn('id', $substitutes)->values();

                return [
                    'startXI' => collect($lineups['startXI'])
                        ->map(function ($lineup) use ($startXIRatings) {
                            $player = $startXIRatings
                                ->sole(function ($player) use ($lineup) {
                                    return $player['id'] === $lineup['id'];
                                });
                            
                            return array_merge((array) $lineup, $player);
                        })
                        ->reverse()
                        ->groupBy(function ($player) {
                            return Str::before($player['grid'], ':');
                        })
                        ->values(),
                    'substitutes' => collect($lineups['substitutes'])
                        ->whereIn('id', $substituteRatings->pluck('id'))
                        ->map(function ($lineup) use ($substituteRatings) {
                            $player = $substituteRatings
                                ->sole(function ($player) use ($lineup) {
                                    return $player['id'] === $lineup['id'];
                                });
                            
                            return array_merge((array) $lineup, $player);
                        })
                ];
            }

            return $lineups;
        })
        ->except('players');
        
        return $result;
    }
        
    /**
     * fixtureの必要なデータを抽出する
     *
     * @param  mixed $data
     * @return array
     */
    private function fixture($data): array
    {
        return [
            'id'             => $data->id,
            'first_half_at'  => date('Y-m-d H:i', $data->periods->first),
            'second_half_at' => date('Y-m-d H:i', $data->periods->second),
            'is_end'         => $data->status->long === self::END_STATUS
        ];
    }

    private function teams($data): array
    {
        return collect($data)
            ->map(function ($team) {
                return [
                    'id'   => $team->id,
                    'name' => $team->name,
                    'img'  => $this->teamImage->generatePath($team->id),
                    'winner' => $team->winner
                ];
            })
            ->toArray();
    }
    
    /**
     * leagueの必要なデータを抽出する
     *
     * @param  mixed $data
     * @return array
     */
    private function league($data): array
    {
        return [
            'id'     => $data->id,
            'name'   => $data->name,
            'season' => $data->season,
            'round'  => $data->round,
            'img'    => $this->leagueImage->generatePath($data->id)
        ];
    }
    
    /**
     * scoreの必要なデータを抽出する
     *
     * @param  mixed $data
     * @return array
     */
    private function score($data): array
    {
        return collect($data)->except('halftime')->toArray();
    }
    
    /**
     * lineupsの必要なデータを抽出する
     *
     * @param  mixed $data
     * @return array
     */
    private function lineups($data): array
    {
        $team = $this->chelseaFilter($data);
                
        return $team
            ->only(['startXI', 'substitutes'])
            ->map(function ($lineups) {
                return collect($lineups)
                    ->map(function ($lineup) {
                        return [
                            'id'       => $lineup->player->id,
                            'name'     => $lineup->player->name,
                            'number'   => $lineup->player->number,
                            'position' => PositionType::from($lineup->player->pos)->name,
                            'grid'     => $lineup->player->grid,
                            'img'      => $this->playerImage->generatePath($lineup->player->id)
                        ];
                    });
            })
            ->toArray();
        
    }
    
    /**
     * 出場した選手のみのデータを抽出する
     *
     * @param  mixed $data
     * @return array
     */
    private function players($data): array
    {
        $Chelsea = $this->chelseaFilter($data);

        return collect($Chelsea['players'])
            ->reject(function ($players) {
                return !$players->statistics[0]->games->minutes;
            })
            ->map(function ($players) {
                return [
                    'id' => $players->player->id,
                    'name' => $players->player->name,
                    'goal' => $players->statistics[0]->goals->total, 
                    'assists' => $players->statistics[0]->goals->assists, 
                    'defaultRating' => $players->statistics[0]->games->rating,
                ];
            })
            ->toArray();
    }
    
    /**
     * チェルシーのデータのみを取得する
     *
     * @param  mixed $data
     * @return Collection
     */
    private function chelseaFilter($data): Collection
    {
        $chelsea = collect($data)
            ->sole(function ($teams) {
                return $teams->team->id === self::CHELSEA_TEAM_ID;
            });

        return collect($chelsea);
    }
}