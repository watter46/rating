<?php declare(strict_types=1);

namespace Tests\Unit\UseCases\Admin\Player;

use Tests\TestCase;
use Illuminate\Support\Carbon;

use App\Models\PlayerInfo;
use App\UseCases\Admin\Player\UpdatePlayerInfos\UpdateFlashLiveSportsIds;
use Database\Seeders\Tests\Admin\PlayerInfosSeeder;


class UpdateFlashLiveSportsIdsTest extends TestCase
{
    protected $seeder = PlayerInfosSeeder::class;

    public function setUp(): void
    {
        Carbon::setTestNow('2024-06-01');

        parent::setUp();
    }
    
    public function test_DBに保存されている()
    {
        PlayerInfo::query()
            ->where('api_football_id', 116117)
            ->first()
            ->update([
                'flash_live_sports_id' => null,
                'flash_live_sports_image_id' => null,
            ]);
        
        $this->assertDatabaseCount('player_infos', 48);

        $playerInfo = PlayerInfo::query()
            ->where('api_football_id', 116117)
            ->first();

        $this->assertNull($playerInfo->flash_live_sports_id);
        $this->assertNull($playerInfo->flash_live_sports_image_id);
    }
    
    public function test_FlashLiveSportsのデータで存在するPlayerInfoをすべてアップデートできる(): void
    {
        /** @var UpdateFlashLiveSportsIds $updateFlashLiveSportsIds */
        $updateFlashLiveSportsIds = app(UpdateFlashLiveSportsIds::class);

        $updateFlashLiveSportsIds->execute();

        $playerInfo = PlayerInfo::query()
            ->where('api_football_id', 116117)
            ->first();

        $this->assertSame('tIqaAkDs', $playerInfo->flash_live_sports_id);
        $this->assertSame('6P6kTBYg-KM9QlZK7', $playerInfo->flash_live_sports_image_id);
    }
}
