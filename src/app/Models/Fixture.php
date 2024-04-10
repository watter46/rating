<?php declare(strict_types=1);

namespace App\Models;

use App\Events\FixtureRegistered;
use App\Events\FixturesRegistered;
use Illuminate\Database\Eloquent\Casts\AsCollection;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

use App\Models\FixtureQueryBuilder;
use App\UseCases\Admin\Fixture\FixtureData\FixtureData;
use App\UseCases\Admin\Fixture\FixturesData\FixturesData;
use App\UseCases\User\Fixture\UserFixtureData;

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

    private const RATE_PERIOD_DAY = 50;
    public  const RATE_PERIOD_EXPIRED_MESSAGE = 'Rate period has expired.';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id',
        'external_fixture_id',
        'external_league_id',
        'season',
        'score',
        'date',
        'fixture',
        'status'
    ];

    protected $casts = [
        'score'   => AsCollection::class,
        'fixture' => AsCollection::class
    ];
    
    /**
     * 試合で使用するデータがすべて存在するか確認して
     * 存在しない場合、不足しているデータを取得するイベントを発行する
     *
     * @param  FixtureData $fixtureData
     * @return void
     */
    public function fixtureRegistered(FixtureData $fixtureData): void
    {
        if ($fixtureData->checkRequiredData()) {
            return;
        }
        
        FixtureRegistered::dispatch($fixtureData);
    }
    
    /**
     * 試合の一覧で使用するデータがすべて存在するか確認して
     * 存在しない場合、不足しているデータを取得するイベントを発行する
     *
     * @param  FixturesData $fixturesData
     * @return void
     */
    public function fixturesRegistered(FixturesData $fixturesData): void
    {
        if ($fixturesData->checkRequiredData()) {
            return;
        }
        
        FixturesRegistered::dispatch($fixturesData);
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

    public function toFixtureData(): UserFixtureData
    {
        return UserFixtureData::create($this->fixture);
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
    
    /**
     * 評価した選手のみ取得する
     *
     * @return HasMany
     */
    public function ratedPlayers(): HasMany
    {
        return $this->hasMany(Player::class)->whereNotNull('rating');
    }
}