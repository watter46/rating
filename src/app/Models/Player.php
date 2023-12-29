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
        'rating'
    ];

    public function evaluate(float $rating): self
    {
        $this->rating = $rating;

        return $this;
    }

    public function associatePlayer(string $fixtureId, string $playerInfoId): self
    {
        $this->fixture_id = $fixtureId;
        $this->player_info_id = $playerInfoId;

        return $this;
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
     * Fixture
     *
     * @return BelongsTo
     */
    public function fixture(): BelongsTo
    {
        return $this->belongsTo(Fixture::class);
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
