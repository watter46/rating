<?php declare(strict_types=1);

namespace App\Models;

use App\UseCases\Util\Season;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;


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
     * 試合に出場した選手を取得する
     *
     * @param  Builder<PlayerInfo> $query
     * @param  Fixture $fixture
     * @return void
     */
    public function scopeLineups(Builder $query, Fixture $fixture): void
    {
        $lineupIdList = collect($fixture->fixture['lineups'])
            ->dot()
            ->filter(function ($p, $key) {
                return Str::afterLast($key, '.') === 'id';
            })
            ->values()
            ->toArray();
                
        $query->whereIn('foot_player_id', $lineupIdList);
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
    
    /**
     * players
     *
     * @return HasMany
     */
    public function players(): HasMany
    {
        return $this->hasMany(Player::class);
    }
}
