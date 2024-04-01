<?php declare(strict_types=1);

namespace App\Models;

use App\Events\FixtureRegistered;
use Illuminate\Database\Eloquent\Casts\AsCollection;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

use App\Models\FixtureQueryBuilder;
use App\UseCases\Admin\Fixture\FixtureData\FixtureData;
use App\UseCases\Admin\Fixture\FixtureData\FixtureDataProcessor;
use Illuminate\Database\Eloquent\Casts\Attribute;

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
        'fixture' => AsCollection::class
    ];

    protected function date(): Attribute
    {
        return Attribute::make(
            get: fn ($date) => Carbon::parse($date)
        );
    }
    
    /**
     * Fixtureで使用するデータがすべて存在するか確認して
     * 存在しない場合、不足しているデータを取得するイベントを発行する
     *
     * @param  FixtureData $fixtureData
     * @return void
     */
    public function registered(FixtureData $fixtureData): void
    {
        if ($fixtureData->checkRequiredData()) {
            return;
        }
        
        FixtureRegistered::dispatch($fixtureData);
    }
    
    /**
     * Fixtureを更新する
     *
     * @param  FixtureData $fixtureData
     * @return self
     */
    public function updateFixture(FixtureData $fixtureData): self
    {
        $this->fixture = $fixtureData->build();

        return $this;
    }
    
    /**
     * Fixtureのデータが有効かどうか
     *
     * @return bool
     */
    public function isValid(): bool
    {
        return FixtureStatusType::from($this->status)->isFinished();
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
     * 試合に出場した選手を紐づける
     *
     * @return HasMany
     */
    public function players(): HasMany
    {
        return $this->hasMany(Player::class);
    }
}
