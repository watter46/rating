<?php declare(strict_types=1);

namespace Tests\Unit\UseCases\Admin\Fixture;

use Tests\TestCase;
use Illuminate\Support\Carbon;

use App\Models\FixtureInfo;
use App\UseCases\Admin\Fixture\FetchFixtureInfos;
use Database\Seeders\Tests\Admin\TestingFixtureInfosSeeder;


class FetchFixtureInfosTest extends TestCase
{
    /** 
     * テスト用全Fixture数: 17
     * paginator取得数: 15
     * outSeason数: 3
     */

    protected $seeder = TestingFixtureInfosSeeder::class;

    public function setUp(): void
    {
        Carbon::setTestNow('2024-05-01');

        parent::setUp();
    }
    
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