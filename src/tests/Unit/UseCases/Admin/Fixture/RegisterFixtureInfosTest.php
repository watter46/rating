<?php declare(strict_types=1);

namespace Tests\Unit\UseCases\Admin\Fixture;

use Illuminate\Support\Carbon;
use Tests\TestCase;

use App\UseCases\Admin\Fixture\RegisterFixtureInfos;


class RegisterFixtureInfosTest extends TestCase
{
    public function setUp(): void
    {
        Carbon::setTestNow(Carbon::parse('2023-11-11'));
        
        parent::setUp();
    }

    public function test_試合日程が複数保存される(): void
    {
        /** @var RegisterFixtureInfos $registerFixtureInfos */
        $registerFixtureInfos = app(RegisterFixtureInfos::class);

        $registerFixtureInfos->execute();

        $this->assertDatabaseCount('fixture_infos', 3);
    }
}
