<?php declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Rating
 * 
 * @property float $rating
 */
class Rating extends Model
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
        'foot_player_id',
        'rating'
    ];

    public function evaluate(int $playerId, float $rating): self
    {
        $this->foot_player_id = $playerId;
        $this->rating = $rating;

        return $this;
    }
    
    /**
     * idでプレイヤーを検索する
     *
     * @param  Builder<Rating> $query
     * @param  int $playerId
     * @return void
     */
    public function scopePlayer(Builder $query, int $playerId): void
    {
        $query->where('foot_player_id', $playerId);
    }
    
    /**
     * fixture
     *
     * @return BelongsTo
     */
    public function fixture(): BelongsTo
    {
        return $this->belongsTo(Fixture::class);
    }
}
