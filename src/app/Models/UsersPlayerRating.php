<?php declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;


class UsersPlayerRating extends Pivot
{
    use HasFactory;
    use HasUlids;

    protected $table = 'users_player_ratings';
    
    public $incrementing = false;
    
    protected $keyType = 'string';

    public $timestamps = false;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'rating',
        'mom'
    ];

    protected $casts = [
        'mom' => 'boolean'
    ];

    public const UPSERT_UNIQUE = ['id'];

    /**
     * ManOfTheMatchの選手を取得する
     *
     * @param  Builder<Player> $query
     * @param  string $fixtureInfoId
     * @return void
     */
    public function scopeByFixtureInfo(Builder $query, string $fixtureInfoId)
    {
        $query->where('fixture_info_id', $fixtureInfoId);
    }
}