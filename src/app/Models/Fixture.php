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
use App\Models\Scopes\CurrentUserScope;
use App\UseCases\Admin\Fixture\FixtureData\FixtureData;
use App\UseCases\Admin\Fixture\FixtureInfoData\FixtureInfoData;
use App\UseCases\Admin\Fixture\FixturesData\FixturesData;
use App\UseCases\User\Fixture\UserFixtureData;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Auth;

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

    public function scopeFixtureInfoId(Builder $query, string $fixtureInfoId)
    {
        $query->where('fixture_info_id', $fixtureInfoId);
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