<?php declare(strict_types=1);

namespace Tests\Unit\UseCases\Admin\UsersPlayerRating;

use Tests\TestCase;
use Illuminate\Support\Carbon;

use App\Models\FixtureInfo;
use App\Models\UsersPlayerRating as UsersPlayerRatingModel;
use App\UseCases\Admin\UsersPlayerRating\UpdateUsersPlayerRating;
use Database\Seeders\Tests\Admin\RatedByUsersSeeder;


class UpdateUsersPlayerRatingTest extends TestCase
{
    protected $seeder = RatedByUsersSeeder::class;

    public function setUp(): void
    {
        Carbon::setTestNow('2024-06-01');

        parent::setUp();
    }
    
    public function test_テスト用データが保存されている()
    {
        $this->assertDatabaseCount('users', 10);
        $this->assertDatabaseCount('fixture_infos', 1);
        $this->assertDatabaseCount('player_infos', 16);
        $this->assertDatabaseCount('users_player_ratings', 16);
        $this->assertDatabaseCount('fixtures', 10);
        $this->assertDatabaseCount('players', 50);
    }
    
    public function test_ユーザー全てが評価した選手ごとのレーティングとMOMの平均を求められる(): void
    {
        /** @var UpdateUsersRating $updateUsersRating */
        $updateUsersRating = app(UpdateUsersPlayerRating::class);

        $updateUsersRating->execute(FixtureInfo::select('id')->first()->id);

        $usersPlayerRating = UsersPlayerRatingModel::whereNotNull('rating')->get();
        
        $this->assertSame(6.3, $usersPlayerRating[0]['rating']);
        $this->assertSame(7.0, $usersPlayerRating[1]['rating']);
        $this->assertSame(7.3, $usersPlayerRating[2]['rating']);
        $this->assertSame(7.0, $usersPlayerRating[3]['rating']);
        $this->assertSame(7.9, $usersPlayerRating[4]['rating']);

        $this->assertTrue($usersPlayerRating[2]['mom']);
    }
}
