<?php declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

use App\UseCases\Util\Season;
use App\Events\PlayerInfoRegistered;
use App\UseCases\Admin\Data\FlashLiveSports\TeamSquad;


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
        'api_football_id',
        'flash_live_sports_id',
        'flash_live_sports_image_id',
        'fixture_info_id'
    ];

    public const SELECT_COLUMNS = 'playerInfos:id,api_football_id';

    public const UPSERT_UNIQUE = ['id'];

    public const UPSERT_ALL = [
        'name',
        'number',
        'season',
        'api_football_id',
        'flash_live_sports_id'
    ];

    /**
     * 試合で使用するデータがすべて存在するか確認して
     * 存在しない場合、不足しているデータを取得するイベントを発行する
     *
     * @param  TeamSquad $teamSquad
     * @return void
     */
    public static function upserted(TeamSquad $teamSquad): void
    {
        if ($teamSquad->check()) return;
        
        PlayerInfoRegistered::dispatch($teamSquad);
    }
    
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
                
        $query->whereIn('api_football_id', $lineupIdList);
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

    public function fixture(): BelongsTo
    {
        return $this->belongsTo(Fixture::class);
    }
    
    /**
     * playerInfos
     *
     * @return BelongsToMany
     */
    public function players(): BelongsToMany
    {
        return $this->belongsToMany(PlayerInfo::class);
    }
}
