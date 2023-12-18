<?php declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Fixture extends Model
{
    use HasFactory;
    use HasUlids;

    public $incrementing = false;
    
    protected $keyType = 'string';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'external_fixture_id',
        'external_team_id',
        'team_name',
        'external_league_id',
        'league_name',
        'round',
        'season',
        'is_end',
        'is_home',
        'home',
        'away',
        'first_half_at',
        'second_half_at'
    ];
}
