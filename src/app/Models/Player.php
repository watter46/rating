<?php declare(strict_types=1);

namespace App\Models;

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
     * ApiPlayer
     *
     * @return BelongsTo
     */
    public function apiPlayer(): BelongsTo
    {
        return $this->belongsTo(ApiPlayer::class);
    }
}
