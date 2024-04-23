<?php declare(strict_types=1);

namespace App\Models;

use App\Events\FixtureInfoRegistered;
use App\Events\FixtureInfosRegistered;
use App\UseCases\Admin\Fixture\FixtureInfoData\FixtureInfoData;
use App\UseCases\Admin\Fixture\FixtureInfosData\FixtureInfosData;
use Illuminate\Database\Eloquent\Casts\AsCollection;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;


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

    /**
     * Fixtureを更新する
     *
     * @param  FixtureInfoData $fixtureInfoData
     * @return self
     */
    public function updateLineups(FixtureInfoData $fixtureInfoData): self
    {
        $data = $fixtureInfoData->buildLineups();
        
        $this->lineups = $data->get('lineups');

        return $this;
    }

    /**
     * 試合で使用するデータがすべて存在するか確認して
     * 存在しない場合、不足しているデータを取得するイベントを発行する
     *
     * @param  FixtureInfoData $fixtureInfoData
     * @return void
     */
    public static function fixtureRegistered(FixtureInfoData $fixtureInfoData): void
    {
        if ($fixtureInfoData->checkRequiredData()) {
            return;
        }
        
        FixtureInfoRegistered::dispatch($fixtureInfoData);
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

    public function lineup(): BelongsTo
    {
        return $this->belongsTo(Lineup::class);
    }

    public function playerInfos(): HasMany
    {
        return $this->hasMany(PlayerInfo::class);
    }
}