<?php declare(strict_types=1);

namespace App\Models;

use App\Events\FixtureRegistered;
use App\UseCases\Util\Season;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\AsCollection;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;


class Fixture extends Model
{
    use HasFactory;
    use HasUlids;

    public $incrementing = false;
    
    protected $keyType = 'string';

    private const PREMIER_LEAGUE_ID = 39;
    private const FA_CUP_ID = 45;
    private const LEAGUE_CUP_ID = 48;

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

    public function updateFixture(Collection $fixture): self
    {
        $this->fixture = $fixture;

        return $this;
    }

    public function registered(): void
    {
        FixtureRegistered::dispatch($this);
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
                self::PREMIER_LEAGUE_ID,
                self::FA_CUP_ID,
                self::LEAGUE_CUP_ID
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
