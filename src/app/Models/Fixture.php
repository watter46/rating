<?php declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\AsCollection;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Collection;

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
        'external_league_id',
        'season',
        'is_end',
        'date',
        'fixture'
    ];

    protected $casts = [
        'fixture' => AsCollection::class
    ];

    public function updateFixture(Collection $fixture): self
    {
        $this->fixture = $fixture;

        return $this;
    }
    
    /**
     * lineup
     *
     * @return HasOne
     */
    public function lineup(): HasOne
    {
        return $this->hasOne(Lineup::class);
    }
}
