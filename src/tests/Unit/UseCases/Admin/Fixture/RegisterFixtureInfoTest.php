<?php declare(strict_types=1);

namespace Tests\Unit\UseCases\Admin\Fixture;

use Database\Seeders\Tests\Admin\TestingFixtureInfoSeeder;
use Illuminate\Support\Carbon;
use Tests\TestCase;

use App\Models\FixtureInfo;
use App\UseCases\Admin\Fixture\RegisterFixtureInfo;


class RegisterFixtureInfoTest extends TestCase
{
    protected $seeder = TestingFixtureInfoSeeder::class;

    public function setUp(): void
    {
        Carbon::setTestNow(Carbon::parse('2023-11-11'));

        parent::setUp();
    }
    
    public function test_指定の試合の試合内容を更新できる(): void
    {
        $fixtureInfo = FixtureInfo::query()
            ->where('api_fixture_id', 1035480)
            ->first();
        
        // Lineups
        $this->assertNull($fixtureInfo->lineups);

        // Fixture
        $this->assertFalse($fixtureInfo->fixture['is_end']);
        $this->assertNull($fixtureInfo->fixture['winner']);

        // Score
        $this->assertNull($fixtureInfo->score['fulltime']['away']);
        $this->assertNull($fixtureInfo->score['fulltime']['home']);

        // Status
        $this->assertFalse($fixtureInfo->is_end);

        /** @var RegisterFixtureInfo $registerFixtureInfo */
        $registerFixtureInfo = app(RegisterFixtureInfo::class);

        $result = $registerFixtureInfo->execute($fixtureInfo->id);

        // Lineups
        $this->assertNotNull($result->lineups);

        // Fixture
        $this->assertTrue($result->fixture['is_end']);
        $this->assertTrue($result->fixture['winner']);

        // Score
        $this->assertSame(3, $result->score['fulltime']['away']);
        $this->assertSame(4, $result->score['fulltime']['home']);

        // Status
        $this->assertTrue($result->is_end);
    }
}
