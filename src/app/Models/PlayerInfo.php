<?php declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

use App\UseCases\Util\Season;
use App\UseCases\Admin\Player\Processors\PlayerInfos\PlayerInfosBuilder;


class PlayerInfo extends Model
{
    use HasFactory;
    use HasUlids;

    public $incrementing = false;
    public $timestamps   = false;
    
    protected $keyType = 'string';

    protected $guarded= ['id'];

    public const SELECT_COLUMNS = 'playerInfos:id,api_player_id';

    public const UPSERT_UNIQUE = ['id'];

    public const UPSERT_API_FOOTBALL_COLUMNS = [
        'api_player_id'
    ];

    public const UPSERT_FLASH_LIVE_SPORTS_COLUMNS = [
        'flash_id',
        'flash_image_id'
    ];

    public function playerInfosBuilder(): PlayerInfosBuilder
    {
        return PlayerInfosBuilder::create();
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
                
        $query->whereIn('api_player_id', $lineupIdList);
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

    public function fixtureInfo()
    {
        return $this->belongsToMany(FixtureInfo::class, 'users_player_statistics')
            ->withPivot('rating', 'comment');
    }
}