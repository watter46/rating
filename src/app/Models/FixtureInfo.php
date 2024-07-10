<?php declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\AsCollection;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

use App\UseCases\Admin\Fixture\Processors\FixtureInfo\FixtureInfoBuilder;
use App\UseCases\Admin\Fixture\Processors\FixtureInfos\FixtureInfosBuilder;


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

    public const UPSERT_UNIQUE = ['id'];

    public const SELECT_COLUMNS = 'fixtureInfo:id,score,teams,league,fixture,lineups';

    public function fixtureInfoBuilder(): FixtureInfoBuilder
    {
        return FixtureInfoBuilder::create($this);
    }

    public function fixtureInfosBuilder(): FixtureInfosBuilder
    {
        return FixtureInfosBuilder::create();
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