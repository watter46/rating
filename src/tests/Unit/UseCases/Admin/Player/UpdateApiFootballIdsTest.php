<?php declare(strict_types=1);

namespace Tests\Unit\UseCases\Admin\Player;

use Tests\TestCase;

use App\UseCases\Admin\Player\UpdatePlayerInfos\UpdateApiFootBallIds;
use Database\Seeders\Tests\Admin\PlayerInfosRemovedSeeder;


class UpdateApiFootballIdsTest extends TestCase
{
    protected $seeder = PlayerInfosRemovedSeeder::class;
    
    public function test_DBに保存されている()
    {
        $this->assertDatabaseCount('player_infos', 47);
    }
    
    public function test_ApiFootballのデータで存在するPlayerInfoをすべてアップデートできる(): void
    {
        /** @var UpdateApiFootBallIds $updateApiFootballIds */
        $updateApiFootballIds = app(UpdateApiFootBallIds::class);

        $updateApiFootballIds->execute();

        $this->assertDatabaseCount('player_infos', 48);
    }
}