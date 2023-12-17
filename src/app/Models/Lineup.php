<?php declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\AsCollection;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

/**
 * 先発メンバー
 *
 * @property integer $fixture_id
 * @property Collection $lineup
 */
class Lineup extends Model
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
        'fixture_id',
        'lineup'
    ];

    protected $casts = [
        'lineup' => AsCollection::class
    ];

    public function setLineup(int $fixture_id, string $lineup): self
    {
        $this->fixture_id = $fixture_id;
        $this->lineup     = $lineup;

        return $this;
    }
}