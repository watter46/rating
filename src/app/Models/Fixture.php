<?php declare(strict_types=1);

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\AsCollection;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

use App\Events\FixtureRegistered;
use App\Http\Controllers\TournamentType;
use App\UseCases\Util\Season;

/**
 * FixtureModel
 * 
 * @property int $external_fixture_id
 * @property int $external_league_id
 * @property int $season
 * @property Collection $score
 * @property bool $is_end
 * @property date $date
 * @property Collection $fixture
 */
class Fixture extends Model
{
    use HasFactory;
    use HasUlids;

    public $incrementing = false;
    
    protected $keyType = 'string';

    private const EVALUATION_PERIOD_DAY = 3;
    public  const EVALUATION_PERIOD_EXPIRED_MESSAGE = 'Evaluation period has expired.';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'external_fixture_id',
        'external_league_id',
        'season',
        'score',
        'is_end',
        'date',
        'fixture'
    ];

    protected $casts = [
        'score' => AsCollection::class,
        'fixture' => AsCollection::class,
    ];
    
    /**
     * Fixtureカラムを更新する
     *
     * @param  Collection $fixture
     * @return self
     */
    public function updateFixture(Collection $fixture): self
    {
        $this->fixture = $fixture;

        return $this;
    }
    
    /**
     * 試合で使用するデータを保存するイベントを発行する
     *
     * @return void
     */
    public function registered(): void
    {
        FixtureRegistered::dispatch($this);
    }
    
    /**
     * 指定した試合でプレイヤーを評価できるか判定する
     *
     * @return bool
     */
    public function canEvaluate(): bool
    {
        $specifiedDate = Carbon::parse($this->date);
        
        return $specifiedDate->diffInDays(now()) <= self::EVALUATION_PERIOD_DAY;
    }

    /**
     * ツアーでソートする
     *
     * @param  Builder<Fixture> $query
     * @param  TournamentType $tournament
     * @return void
     */
    public function scopeTournament(Builder $query, TournamentType $tournament): void
    {        
        if ($tournament->isAll()) return;

        $query->where('external_league_id', $tournament->toId());
    }

    /**
     * シーズン中の試合のみ取得する
     *
     * @param  Builder<Fixture> $query
     * @return void
     */
    public function scopeInSeason(Builder $query): void
    {
        $query
            ->whereIn('external_league_id', [
                TournamentType::PREMIER_LEAGUE->toId(),
                TournamentType::FA_CUP->toId(),
                TournamentType::LEAGUE_CUP->toId()
            ]);
    }

    /**
     * 今日までの試合のみ取得する
     *
     * @param  Builder<Fixture> $query
     * @return void
     */
    public function scopePast(Builder $query): void
    {
        $query
            ->select(['id', 'score', 'date', 'external_fixture_id', 'fixture'])
            ->currentSeason()
            ->whereDate('date', '<=', now())
            ->orderBy('date', 'desc');
    }

    /**
     * 今シーズンのみ取得する
     *
     * @param  Builder<Fixture> $query
     * @return void
     */
    public function scopeCurrentSeason(Builder $query): void
    {
        $query->where('season', Season::current());
    }
    
    /**
     * ratings
     *
     * @return HasMany
     */
    public function players(): HasMany
    {
        return $this->hasMany(Player::class);
    }
}
