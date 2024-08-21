<?php declare(strict_types=1);

namespace Database\Seeders\Tests\Admin;

use Illuminate\Database\Seeder;

use App\Http\Controllers\Util\TestPlayerInfosFile;
use App\Models\PlayerInfo;


class PlayerInfosRemovedSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $file = new TestPlayerInfosFile;

        $data = $file
            ->get()
            ->reject(fn($player) => $player['api_player_id'] === 116117);
        
        PlayerInfo::upsert($data->toArray(), ['id']);
    }
}
