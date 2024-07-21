<?php declare(strict_types=1);

namespace Tests\Unit\Scheduler;

use Tests\TestCase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Artisan;
use Database\Seeders\Tests\Admin\RatedByUsersSeeder;
use Illuminate\Support\Facades\Cache;

use App\Models\PlayerInfo;
use App\Models\UsersPlayerStatistic;


class SchedulerTest extends TestCase
{
    protected $seeder = RatedByUsersSeeder::class;
    
    public function setUp(): void
    {
        Carbon::setTestNow('2023-11-11 00:00');
        
        parent::setUp();
    }

    public function test_テスト用データが保存されている()
    {
        $this->assertDatabaseCount('users', 10);
        $this->assertDatabaseCount('fixture_infos', 1);
        $this->assertDatabaseCount('player_infos', 16);
        $this->assertDatabaseCount('users_player_statistics', 16);
        $this->assertDatabaseCount('fixtures', 10);
        $this->assertDatabaseCount('players', 50);
    }

    public function test_0時にFixtureInfoとPlayerInfoとUsersRatingがUpdateされる()
    {
        Cache::store('redis')->clear();

        Artisan::call('schedule:run');

        $this->assertDatabaseCount('fixture_infos', 3);
        $this->assertDatabaseCount('player_infos', 48);

        $playerInfo = PlayerInfo::query()
            ->where('api_football_id', 116117)
            ->first();

        $this->assertSame('tIqaAkDs', $playerInfo->flash_live_sports_id);
        $this->assertSame('6P6kTBYg-KM9QlZK7', $playerInfo->flash_live_sports_image_id);

        $statistics = UsersPlayerStatistic::whereNotNull('rating')->get();

        $this->assertSame(6.3, $statistics[0]['rating']);
        $this->assertSame(7.0, $statistics[1]['rating']);
        $this->assertSame(7.3, $statistics[2]['rating']);
        $this->assertSame(7.0, $statistics[3]['rating']);
        $this->assertSame(7.9, $statistics[4]['rating']);

        $this->assertTrue($statistics[2]['mom']);
    }
}