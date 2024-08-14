<?php declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Schema;

use App\Models\TournamentType;
use App\UseCases\Admin\Data\ApiFootball\FixtureData\FixtureStatusType;
use App\UseCases\Util\Season;
use Illuminate\Support\Collection;

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
            ->whereNull('lineups');
    }

    /**
     * 指定の期間内の試合を取得する
     *
     * @return Builder
     */
    public function last(): Builder
    {
        return $this
            ->whereDate('date', '<=', now('UTC'))
            ->where('status', FixtureStatusType::MatchFinished)
            ->orderBy('date', 'desc');
    }

    /**
     * 1か月以内の試合から取得する
     *
     * @return Builder
     */
    public function withinOneMonth(): Builder
    {
        return $this->where('date', '>=', now('UTC')->subMonth());
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
     * 指定したカラム名以外を取得する
     *
     * @param  array $except
     * @return Builder
     */
    public function selectWithout(array $except = []): Builder
    {
        return $this->select(
                $this->withOutTimeStamp()
                    ->flip()
                    ->except($except)
                    ->flip()
                    ->toArray()
            );
    }

    private function withOutTimeStamp(): Collection
    {
        return collect(Schema::getColumnListing('fixture_infos'))
            ->flip()
            ->except(['created_at', 'updated_at'])
            ->flip();
    }
}