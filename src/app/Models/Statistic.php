<?php declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\AsCollection;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

/**
 * 試合の統計
 *
 * @property integer $fixture_id
 * @property Collection $statistic
 */
class Statistic extends Model
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
        'statistic'
    ];

    protected $casts = [
        'statistic' => AsCollection::class
    ];

    public function setStatistic(int $fixture_id, string $statistic): self
    {
        $this->fixture_id = $fixture_id;
        $this->statistic  = $statistic;

        return $this;
    }
}
