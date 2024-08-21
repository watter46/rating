<?php declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\AsCollection;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Collection;


class FixtureInfo extends Model
{
    use HasFactory;
    use HasUlids;

    public $incrementing = false;
    
    protected $keyType = 'string';

    protected $guarded = ['id'];
    
    protected $casts = [
        'is_end' => 'boolean',
        'score' => AsCollection::class,
        'teams' => AsCollection::class,
        'league' => AsCollection::class,
        'fixture' => AsCollection::class,
        'lineups' => AsCollection::class,
    ];

    public const UPSERT_UNIQUE = ['id'];

    public const UPSERT_COLUMNS = [
        'date',
        'is_end',
        'score',
        'teams',
        'league',
        'fixture'
    ];

    public const SELECT_COLUMNS = 'fixtureInfo:id,score,teams,league,fixture,lineups';

    public function castsToJson(): Collection
    {        
        $jsonKeys = collect([
                'score',
                'teams',
                'league',
                'fixture',
                'lineups'
            ]);
        
        return collect($this)
            ->map(function ($value, $key) use ($jsonKeys) {
                if (!$value) {
                    return $value;
                }
                
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
        return $this->belongsToMany(PlayerInfo::class, 'users_player_ratings')
            ->using(UsersPlayerRating::class)
            ->withPivot('id', 'rating', 'mom', 'fixture_info_id', 'player_info_id')
            ->as('users_player_rating');
    }
}