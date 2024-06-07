<?php declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


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
        'rate_count',
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

    /**
     * デフォルト値
     *
     * @var array
     */
    protected $attributes = [
        'rating' => null,
        'mom' => false,
        'rate_count' => 0
    ];

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
        
        $this->rate_count++;
        
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
    public function scopeFixtureInfoId(Builder $query, string $fixtureInfoId)
    {
        $query->where('fixture_info_id', $fixtureInfoId);
    }
    
    /**
     * Fixture
     *
     * @return BelongsTo
     */
    public function fixture(): BelongsTo
    {
        return $this->belongsTo(Fixture::class)->withDefault();
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
