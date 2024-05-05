<?php declare(strict_types=1);

namespace App\Models;

use App\Models\TournamentType;
use App\UseCases\Admin\Fixture\Data\FixtureStatusType;
use App\UseCases\Util\Season;
use Illuminate\Database\Eloquent\Builder;

class FixtureInfoQueryBuilder extends Builder
{
    /**
     * ツアーでソートする
     *
     * @param  TournamentType $tournament
     * @return Builder
     */
    public function tournament(TournamentType $tournament): Builder
    {        
        return $this->whereIn('external_league_id', $tournament->toIds());
    }

    /**
     * シーズン中の試合のみ取得する
     *
     * @return Builder
     */
    public function inSeasonTournament(): Builder
    {
        return $this
            ->whereIn('external_league_id', [
                TournamentType::PREMIER_LEAGUE->toIds(),
                TournamentType::FA_CUP->toIds(),
                TournamentType::LEAGUE_CUP->toIds()
            ]);
    }

    /**
     * まだ始まっていない試合のみ取得する
     *
     * @return Builder
     */
    public function notStarted(): Builder
    {
        return $this->where('status', FixtureStatusType::NotStarted->value);
    }
    
    /**
     * 終了している試合のみ取得する
     *
     * @return Builder
     */
    public function finished(): Builder
    {
        return $this->where('status', FixtureStatusType::MatchFinished->value);
    }
    
    /**
     * 次の試合を取得する
     *
     * @return Builder
     */
    public function next(): Builder
    {
        return $this
            ->whereDate('date', '>=', now('UTC'))
            ->orderBy('date')
            ->whereNull('fixture');
    }

    /**
     * 今日までの試合を取得する
     *
     * @return Builder
     */
    public function untilToday(): Builder
    {
        return $this
            ->where('date', '<=', now('UTC'))
            ->orderBy('date', 'desc');
    }

    /**
     * 今シーズンのみ取得する
     *
     * @return Builder
     */
    public function currentSeason(): Builder
    {
        return $this->where('season', Season::current());
    }

    /**
     * 今シーズンのみ取得する
     *
     * @return Builder
     */
    public function selectData(): Builder
    {
        return $this->select('id');
    }
}