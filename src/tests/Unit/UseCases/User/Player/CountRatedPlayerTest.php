<?php declare(strict_types=1);

namespace Tests\Unit\UseCases\User\Player;

use Tests\TestCase;

use App\Models\Fixture;
use App\Models\FixtureInfo;
use App\Models\Player;
use App\Models\PlayerInfo;
use App\UseCases\User\Player\CountRatedPlayer;
use App\UseCases\User\Player\RatePlayer;
use Database\Seeders\Tests\User\TestingFixtureInfoSeeder;


class CountRatedPlayerTest extends TestCase
{
    protected $seeder = TestingFixtureInfoSeeder::class;
    
    public function test_未評価状態の時正常な値が返ってくる(): void
    {
        $fixtureInfo = FixtureInfo::first();
                
        $countRatedPlayer = app(CountRatedPlayer::class);

        $counts = $countRatedPlayer->execute($fixtureInfo->id);

        $this->assertSame(0, $counts['ratedCount']);
        $this->assertSame(16, $counts['playerCount']);
    }

    public function test_評価している状態の時正常な値が返ってくる(): void
    {
        $fixtureInfo = FixtureInfo::query()
            ->with(['playerInfos' => fn ($query) => $query->take(5)])
            ->first();
        
        $playerInfo = $fixtureInfo->playerInfos->first();
        
        $ratePlayer = app(RatePlayer::class);

        $ratePlayer->execute($fixtureInfo->id, $playerInfo->id, 8.0);

        $fixture = Fixture::first();

        Player::query()->delete();
        
        $fixture->players()
            ->saveMany(
                $fixtureInfo->playerInfos
                    ->map(function (PlayerInfo $playerInfo) {
                        return new Player([
                            'rating' => 8.5,
                            'mom' => false,
                            'rate_count' => 1,
                            'player_info_id' => $playerInfo->id
                        ]);
                    })
            );
                
        $countRatedPlayer = app(CountRatedPlayer::class);

        $counts = $countRatedPlayer->execute($fixtureInfo->id);

        $this->assertSame(5, $counts['ratedCount']);
        $this->assertSame(16, $counts['playerCount']);
    }
}