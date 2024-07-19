<?php declare(strict_types=1);

namespace Tests\Unit\UseCases\Admin\Player;

use Database\Seeders\Tests\Admin\RatedByUsersSeeder;
use Tests\TestCase;

use App\Models\FixtureInfo;
use App\Models\UsersPlayerStatistic;
use App\UseCases\Admin\Player\UpdateUsersRating;


class UpdateUsersRatingTest extends TestCase
{
    protected $seeder = RatedByUsersSeeder::class;

    public function test_テスト用データが保存されている()
    {
        $this->assertDatabaseCount('users', 10);
        $this->assertDatabaseCount('fixture_infos', 1);
        $this->assertDatabaseCount('player_infos', 16);
        $this->assertDatabaseCount('users_player_statistics', 16);
        $this->assertDatabaseCount('fixtures', 10);
        $this->assertDatabaseCount('players', 50);
    }
    
    public function test_ユーザー全てが評価した選手ごとのレーティングとMOMの平均を求められる(): void
    {
        /** @var UpdateUsersRating $updateUsersRating */
        $updateUsersRating = app(UpdateUsersRating::class);

        $updateUsersRating->execute(FixtureInfo::select('id')->first()->id);

        $statistics = UsersPlayerStatistic::whereNotNull('rating')->get();
        
        $this->assertSame(6.3, $statistics[0]['rating']);
        $this->assertSame(7.0, $statistics[1]['rating']);
        $this->assertSame(7.3, $statistics[2]['rating']);
        $this->assertSame(7.0, $statistics[3]['rating']);
        $this->assertSame(7.9, $statistics[4]['rating']);

        $this->assertTrue($statistics[2]['mom']);
    }
}
