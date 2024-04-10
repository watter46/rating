<?php declare(strict_types=1);

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

use App\Models\Scopes\CurrentUserScope;
use Illuminate\Support\Facades\Auth;

class Player extends Model
{
    use HasFactory;
    use HasUlids;

    public $incrementing = false;
    public $timestamps   = false;
    
    protected $keyType = 'string';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'rating',
        'mom',
        'player_info_id',
        'fixture_id'
    ];
    
    /**
     * キャスト
     *
     * @var array
     */
    protected $casts = [
        'mom' => 'boolean'
    ];

    protected $hidden = [
        'user_id'
    ];

    /**
     * デフォルト値
     *
     * @var array
     */
    protected $attributes = [
        'rating' => null,
        'mom' => false,
    ];
    
    /**
     * 保存されるときにUserIdを紐づける
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        self::saving(function($player) {
            $player->user_id = Auth::id();
        });
    }

    /**
     * UserBooted
     */
    protected static function booted(): void
    {
        static::addGlobalScope(new CurrentUserScope);
    }

    public function decideMOM(): self
    {
        $this->mom = true;

        return $this;
    }

    public function unDecideMOM(): self
    {
        $this->mom = false;

        return $this;
    }

    public function rate(float $rating): self
    {
        $this->rating = $rating;

        return $this;
    }
    
    /**
     * ManOfTheMatchの選手を取得する
     *
     * @param  Builder<Player> $query
     * @param  string $fixtureId
     * @return void
     */
    public function scopeMom(Builder $query, string $fixtureId)
    {
        $query
            ->fixtureId($fixtureId)
            ->where('mom', true);
    }

    /**
     * playerInfoIdでソートする
     *
     * @param  Builder<Player> $query
     * @param  string $playerInfoId
     * @return void
     */
    public function scopePlayerInfoId(Builder $query, string $playerInfoId)
    {
        $query->where('player_info_id', $playerInfoId);
    }

    /**
     * FixtureIdでソートする
     *
     * @param  Builder<Player> $query
     * @param  string $fixtureId
     * @return void
     */
    public function scopeFixtureId(Builder $query, string $fixtureId)
    {
        $query->where('fixture_id', $fixtureId);
    }
    
    /**
     * Fixture
     *
     * @return BelongsTo
     */
    public function fixture(): BelongsTo
    {
        return $this->belongsTo(Fixture::class);
    }

    /**
     * user
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * playerInfo
     *
     * @return BelongsTo
     */
    public function playerInfo(): BelongsTo
    {
        return $this->belongsTo(PlayerInfo::class);
    }
}
