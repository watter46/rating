<?php declare(strict_types=1);

namespace Tests\Unit\UseCases\User\Fixture;

use App\Models\Fixture;
use App\Models\FixtureInfo;
use App\UseCases\User\Fixture\fetchLatestFixture;
use Tests\TestCase;

use Database\Seeders\Tests\User\TestingFixtureSeeder;
use Illuminate\Support\Carbon;

class FetchLatestFixtureTest extends TestCase
{
    protected $seeder = TestingFixtureSeeder::class;
    
    public function test_DBにデータが存在する(): void
    {
        $this->assertDatabaseCount('fixture_infos', 3);
        $this->assertDatabaseCount('fixtures', 1);
        $this->assertDatabaseCount('player_infos', 1);
        $this->assertDatabaseCount('players', 1);
    }

    public function test_最新の試合を取得する()
    {
        /** @var fetchLatestFixture $fetchLatestFixture */
        $fetchLatestFixture = app(fetchLatestFixture::class);

        $fixture = $fetchLatestFixture->execute();
        
        $dates = FixtureInfo::pluck('date')->map(fn($d) => Carbon::parse($d));

        $latestDate = $dates->filter(function (Carbon $date) {
                return $date->lessThan(now('UTC'));
            })
            ->sortDesc()
            ->first();

        $this->assertTrue(Carbon::parse($fixture->fixtureInfo->date)->isCurrentDay($latestDate));

        // Fixture
        $this->assertSame(0, $fixture->mom_count);
        $this->assertSame(5, $fixture->momLimit);

        // PlayerInfos
        $this->assertCount(1, $fixture->fixtureInfo->playerInfos);

        // Players
        $this->assertCount(1, $fixture->players);
        $this->assertSame(10.0, $fixture->players->first()->rating);
        $this->assertFalse($fixture->players->first()->mom);
        $this->assertSame(1, $fixture->players->first()->rate_count);
        $this->assertSame(3, $fixture->players->first()->rateLimit);
        $this->assertSame(0, $fixture->players->first()->momCount);
        $this->assertSame(5, $fixture->players->first()->momLimit);
        $this->assertTrue($fixture->players->first()->canRate);
        $this->assertTrue($fixture->players->first()->canMom);
        $this->assertFalse($fixture->players->first()->exceedMomLimit);
        $this->assertSame(10.0, $fixture->players->first()->average->rating);
        $this->assertTrue($fixture->players->first()->average->mom);
    }
}
