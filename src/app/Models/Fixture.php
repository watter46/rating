<?php declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

use App\UseCases\User\FixtureDomain;


class Fixture extends Model
{
    use HasFactory;
    use HasUlids;

    public $incrementing = false;
    
    protected $keyType = 'string';

    /**
     * デフォルト値
     *
     * @var array
     */
    protected $attributes = [
        'mom_count' => 0
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'mom_count',
        'fixture_info_id'
    ];

    public function onlyFillable(): self
    {
        return new self(collect($this->toArray())->only($this->fillable)->toArray());
    }

    public function incrementMomCount(): self
    {
        $this->mom_count += 1;

        return $this;
    }

    public function toDomain(): FixtureDomain
    {
        return new FixtureDomain($this);
    }

    public function scopeFixtureInfoId(Builder $query, string $fixtureInfoId)
    {
        $query->where('fixture_info_id', $fixtureInfoId);
    }

    public function scopeSelectWithout(Builder $query, array $except = [])
    {
        $query->select(
            collect($this->fillable )
                ->flip()
                ->except($except)
                ->flip()
                ->merge(['id'])
                ->toArray()
        );
    }

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
     * fixture
     *
     * @return BelongsTo
     */
    public function fixtureInfo(): BelongsTo
    {
        return $this->belongsTo(FixtureInfo::class);
    }

    /**
     * player
     *
     * @return HasMany
     */
    public function ratedPlayers(): HasMany
    {
        return $this->hasMany(Player::class)->whereNotNull('rating');
    }

    public function playerInfos()
    {
        return $this->hasManyThrough(PlayerInfo::class, FixtureInfo::class);
    }

    /**
     * player
     *
     * @return HasMany
     */
    public function players(): HasMany
    {
        return $this->hasMany(Player::class);
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
}