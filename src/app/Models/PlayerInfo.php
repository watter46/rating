<?php declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

use App\UseCases\Util\Season;


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

    public const UPSERT_FLASH_COLUMNS = [
        'name',
        'flash_id',
        'flash_image_id'
    ];

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

    public function player()
    {
        return $this->hasOne(Player::class);
    }
}