<?php declare(strict_types=1);

namespace App\Models;

use Database\Factories\UsersRatingFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Average extends Model
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
        'fixture_info_id'
    ];

    protected $casts = [
        'mom' => 'boolean'
    ];

    protected static function newFactory()
    {
        return UsersRatingFactory::new();
    }

    public function scopeFixtureInfoId(Builder $query, string $fixtureInfoId)
    {
        $query->where('fixture_info_id', $fixtureInfoId);
    }
}
