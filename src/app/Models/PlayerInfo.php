<?php declare(strict_types=1);

namespace App\Models;

use App\UseCases\Util\Season;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;


class PlayerInfo extends Model
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
        'name',
        'number',
        'season',
        'foot_player_id',
        'sofa_image_id'
    ];
    
    /**
     * players
     *
     * @return HasMany
     */
    public function players(): HasMany
    {
        return $this->hasMany(Player::class);
    }

    /**
     * scopeFixture
     *
     * @param  Builder<Player> $query
     * @param  string $fixtureId
     * @return void
     */
    public function scopeByFixture(Builder $query, string $fixtureId)
    {
        $query->where('fixture_id', $fixtureId);
    }

    /**
     * 今シーズンのプレイヤーを検索する
     *
     * @param  Builder<PlayerInfo> $query
     * @return void
     */
    public function scopeCurrentSeason(Builder $query): void
    {
        $query->where('season', Season::current());
    }
}
