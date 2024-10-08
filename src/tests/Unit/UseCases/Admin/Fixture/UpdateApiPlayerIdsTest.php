<?php declare(strict_types=1);

namespace Tests\Unit\UseCases\Admin\Player;

use Tests\TestCase;
use Illuminate\Support\Carbon;

use App\UseCases\Admin\Fixture\UpdateApiPlayerIds;
use Database\Seeders\Tests\Admin\PlayerInfosRemovedSeeder;


class UpdateApiPlayerIdsTest extends TestCase
{
    protected $seeder = PlayerInfosRemovedSeeder::class;
    
    public function setUp(): void
    {
        Carbon::setTestNow('2024-06-01');

        parent::setUp();
    }
    
    public function test_DBに保存されている()
    {
        $this->assertDatabaseCount('player_infos', 47);
    }
    
    public function test_ApiFootballのデータで存在するPlayerInfoをすべてアップデートできる(): void
    {
        /** @var UpdateApiPlayerIds $updateApiPlayerIds */
        $updateApiPlayerIds = app(UpdateApiPlayerIds::class);

        $updateApiPlayerIds->execute();

        $this->assertDatabaseCount('player_infos', 48);
    }
}