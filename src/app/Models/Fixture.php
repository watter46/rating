<?php declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\AsCollection;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

use App\Events\FixtureRegistered;
use App\Http\Controllers\TournamentType;
use App\UseCases\Util\Season;
use App\Models\FixtureQueryBuilder;

/**
 * FixtureModel
 * 
 * @property int $external_fixture_id
 * @property int $external_league_id
 * @property int $season
 * @property Collection $score
 * @property date $date
 * @property Collection $fixture
 */
class Fixture extends Model
{
    use HasFactory;
    use HasUlids;

    public $incrementing = false;
    
    protected $keyType = 'string';

    private const RATE_PERIOD_DAY = 5;
    public  const RATE_PERIOD_EXPIRED_MESSAGE = 'Rate period has expired.';

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
        'date',
        'fixture',
        'status'
    ];

    protected $casts = [
        'score' => AsCollection::class,
        'fixture' => AsCollection::class,
    ];
    
    /**
     * rate
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
     */
    public function registered()
    {
        FixtureRegistered::dispatch($this);
    }
    
    /**
     * 指定した試合でプレイヤーを評価できるか判定する
     * 
     * @return bool
     */
    public function canRate(): bool
    {
        $specifiedDate = Carbon::parse($this->date);

        return $specifiedDate->diffInDays(now('UTC')) <= self::RATE_PERIOD_DAY;
    }

    public static function query(): FixtureQueryBuilder
    {
        return parent::query();
    }

    public function newEloquentBuilder($query): FixtureQueryBuilder
    {
        return new FixtureQueryBuilder($query);
    }
    
    /**
     * players
     *
     * @return HasMany
     */
    public function players(): HasMany
    {
        return $this->hasMany(Player::class);
    }
}
