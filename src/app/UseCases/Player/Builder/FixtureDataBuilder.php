<?php declare(strict_types=1);

namespace App\UseCases\Player\Builder;

use App\Http\Controllers\PositionType;
use App\Http\Controllers\Util\LeagueImageFile;
use App\Http\Controllers\Util\PlayerImageFile;
use App\Http\Controllers\Util\TeamImageFile;
use App\View\Components\Rating\PlayerImage;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

final readonly class FixtureDataBuilder
{
    private const CHELSEA_TEAM_ID = 49;
    private const END_STATUS = 'Match Finished';

    public function __construct(private TeamImageFile $teamImage)
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
            ->except(['events', 'statistics'])
            ->map(function ($data, $key) {
                return match ($key) {
                    'fixture' => $this->fixture($data),
                    'teams'   => $this->teams($data),
                    'league'  => $this->league($data),
                    'goals'   => $this->goals($data),
                    'score'   => $this->score($data),
                    'lineups' => $this->lineups($data),
                    'players' => $this->players($data),
                };
            });
        
        $result = $data->transform(function ($lineups, $key) use ($data) {
            if ($key === 'lineups') {
                $players = $data['players'];
                
                $startXI = collect($lineups['startXI'])->pluck('id');
                $startXIRatings = collect($players)->whereIn('id', $startXI);

                $substitutes = collect($lineups['substitutes'])->pluck('id');
                $substituteRatings = collect($players)->whereIn('id', $substitutes)->values();
                
                return [
                    'startXI' => collect($lineups['startXI'])
                        ->map(function($lineup, $index) use ($startXIRatings) {
                            return array_merge((array) $lineup, $startXIRatings[$index]);
                        })
                        ->reverse()
                        ->groupBy(function ($player) {
                            return Str::before($player['grid'], ':');
                        })
                        ->values(),
                    'substitutes' => collect($lineups['substitutes'])
                        ->map(function ($lineup, $index) use ($substituteRatings) {
                            return array_merge((array) $lineup, $substituteRatings[$index]);
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
                    'img'  => $this->teamImage->generatePath($team->id)
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
            'img'    => LeagueImageFile::generatePath($data->id)
        ];
    }
    
    /**
     * goalsの必要なデータを抽出する
     *
     * @param  mixed $data
     * @return array
     */
    private function goals($data): array
    {
        return (array) $data;
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
                            'img'      => PlayerImageFile::generatePath($lineup->player->id)
                        ];
                    });
            })
            ->toArray();
        
    }
    
    /**
     * playersの必要なデータを抽出する
     *
     * @param  mixed $data
     * @return array
     */
    private function players($data): array
    {
        $team = $this->chelseaFilter($data);

        return collect($team['players'])
            ->map(function ($players) {
                return [
                    'id'     => $players->player->id,
                    'rating' => $players->statistics[0]->games->rating
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