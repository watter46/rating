<?php declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\AsCollection;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

use App\Events\FixtureInfoRegistered;
use App\Events\FixtureInfosRegistered;
use App\UseCases\Admin\Fixture\Data\FixtureStatusType;
use App\UseCases\Admin\Fixture\FixtureInfoData\FixtureInfoData;
use App\UseCases\Admin\Fixture\FixtureInfosData\FixtureInfosData;


class FixtureInfo extends Model
{
    use HasFactory;
    use HasUlids;

    public $incrementing = false;
    
    protected $keyType = 'string';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'external_fixture_id',
        'external_league_id',
        'season',
        'date',
        'status',
        'score',
        'teams',
        'league',
        'fixture',
        'lineups'
    ];

    protected $casts = [
        'score' => AsCollection::class,
        'teams' => AsCollection::class,
        'league' => AsCollection::class,
        'fixture' => AsCollection::class,
        'lineups' => AsCollection::class,
    ];

    public const SELECT_COLUMNS = 'fixtureInfo:id,score,teams,league,fixture,lineups';

    /**
     * Fixtureを更新する
     *
     * @param  FixtureInfoData $fixtureInfoData
     * @return self
     */
    public function updateFixtureInfoData(FixtureInfoData $fixtureInfoData): self
    {        
        $this->lineups = $fixtureInfoData->buildLineups()->get('lineups');
        $this->score   = $fixtureInfoData->buildScore();
        $this->fixture = $fixtureInfoData->buildFixture();
        $this->status  = FixtureStatusType::MatchFinished->value;

        return $this;
    }

    /**
     * 試合で使用するデータがすべて存在するか確認して
     * 存在しない場合、不足しているデータを取得するイベントを発行する
     *
     * @param  FixtureInfoData $fixtureInfoData
     * @return void
     */
    public function fixtureRegistered(FixtureInfoData $fixtureInfoData): void
    {
        if ($fixtureInfoData->equalLineupCount($this->lineupCount) && $fixtureInfoData->checkRequiredData()) {
            return;
        }
                
        FixtureInfoRegistered::dispatch($fixtureInfoData, $this);
    }

    /**
     * 試合の一覧で使用するデータがすべて存在するか確認して
     * 存在しない場合、不足しているデータを取得するイベントを発行する
     *
     * @param  FixtureInfosData $fixtureInfosData
     * @return void
     */
    public static function fixturesRegistered(FixtureInfosData $fixtureInfosData): void
    {
        if ($fixtureInfosData->checkRequiredData()) {
            return;
        }
        
        FixtureInfosRegistered::dispatch($fixtureInfosData);
    }

    public function castsToJson()
    {
        return collect($this)
            ->map(function ($value, $key) {
                $jsonKeys = collect($this->getCasts())->keys();

                if ($jsonKeys->some($key)) {
                    return collect($value)->toJson();
                }

                return $value;
            });
    }

    public static function query(): FixtureInfoQueryBuilder
    {
        return parent::query();
    }

    public function newEloquentBuilder($query): FixtureInfoQueryBuilder
    {
        return new FixtureInfoQueryBuilder($query);
    }

    public function fixture(): HasOne
    {
        return $this->hasOne(Fixture::class);
    }

    public function playerInfos(): BelongsToMany
    {
        return $this->belongsToMany(PlayerInfo::class);
    }
}