<?php declare(strict_types=1);

namespace Tests\Unit\UseCases\Admin\Player;

use Tests\TestCase;

use App\UseCases\Admin\Player\FetchPlayerInfosUseCase;
use Database\Seeders\Tests\Admin\PlayerInfosSeeder;


class FetchPlayerInfosTest extends TestCase
{
    protected $seeder = PlayerInfosSeeder::class;

    public function test_選手全員を取得できる(): void
    {
        /** @var FetchPlayerInfosUseCase $fetchPlayerInfos */
        $fetchPlayerInfos = app(FetchPlayerInfosUseCase::class);

        $playerInfos = $fetchPlayerInfos->execute();

        $this->assertCount(48, $playerInfos);
    }
}
