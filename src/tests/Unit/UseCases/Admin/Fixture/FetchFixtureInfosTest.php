<?php declare(strict_types=1);

namespace Tests\Unit\UseCases\Admin\Fixture;

use Database\Seeders\Tests\Admin\TestingFixtureInfosSeeder;
use Tests\TestCase;

use App\Models\FixtureInfo;
use App\UseCases\Admin\Fixture\FetchFixtureInfos;


class FetchFixtureInfosTest extends TestCase
{
    protected $seeder = TestingFixtureInfosSeeder::class;

    public function test_FixtureInfoが保存されている()
    {
        $this->assertDatabaseCount('fixture_infos', 17);
    }
    
    public function test_Adminで試合の一覧を取得できる(): void
    {
        /** @var FetchFixtureInfos $fetchFixtureInfos */
        $fetchFixtureInfos = app(FetchFixtureInfos::class);

        $fixtureInfos = $fetchFixtureInfos->execute();

        $this->assertCount(15, $fixtureInfos);
        $this->assertCount(12, $fixtureInfos->filter(fn(FixtureInfo $fixtureInfo) => $fixtureInfo->lineupsExists));
    }
}
