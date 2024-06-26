<?php declare(strict_types=1);

namespace Database\Seeders\Tests\Admin;

use Illuminate\Database\Seeder;

use App\Http\Controllers\Util\TestPlayerInfosFile;
use App\Models\PlayerInfo;


class PlayerInfosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $file = new TestPlayerInfosFile;

        PlayerInfo::upsert($file->get()->toArray(), ['id']);
    }
}
