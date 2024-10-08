<?php declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;

use App\Http\Controllers\Util\PlayerInfoFile;
use App\Models\PlayerInfo;


class PlayerInfoSeeder extends Seeder
{
    /** 2023年のPlayerInfoをすべて保存する */
    public function run(): void
    {
        /** @var PlayerInfoFile $playerInfos */
        $playerInfos = app(PlayerInfoFile::class);

        PlayerInfo::upsert($playerInfos->get(2024)->toArray(), PlayerInfo::UPSERT_UNIQUE);
    }
}