<?php declare(strict_types=1);

namespace Tests\Unit\UseCases\User\Player;

use Tests\TestCase;

use App\Models\FixtureInfo;
use App\Models\Player;
use App\UseCases\User\Player\RatePlayer;
use Database\Seeders\Tests\User\TestingFixtureInfoSeeder;


class RatePlayerTest extends TestCase
{
    protected $seeder = TestingFixtureInfoSeeder::class;

    public function test_評価して正常な値が返る(): void
    {
        $fixtureInfo = FixtureInfo::query()
            ->with(['playerInfos' => fn ($query) => $query->first()])
            ->first();
        
        $playerInfo = $fixtureInfo->playerInfos->first();
        
        $ratePlayer = app(RatePlayer::class);

        $rated = $ratePlayer->execute($fixtureInfo->id, $playerInfo->id, 8.0);
        
        $this->assertDatabaseHas('fixtures', [
                'mom_count' => 0
            ]);
        
        $this->assertDatabaseHas('players', [
                'rating' => 8.0,
                'rate_count' => 1
            ]);
        
        $this->assertSame(8.0, $rated->rating);
        $this->assertSame(1, $rated->rate_count);
        $this->assertTrue($rated->canRate);
        $this->assertSame(3, $rated->rateLimit);
    }

    public function test_評価期間外の時に例外を投げる(): void
    {
        // 試合日程を6日前にする
        $fixtureInfo = FixtureInfo::first();
        $fixtureInfo->update(['date' => now('UTC')->subDays(6)]);

        $fixtureInfo = FixtureInfo::query()
            ->with(['playerInfos' => fn ($query) => $query->first()])
            ->first();
        
        $playerInfo = $fixtureInfo->playerInfos->first();
        
        $ratePlayer = app(RatePlayer::class);

        $this->expectExceptionMessage('Rate period has expired.');

        $ratePlayer->execute($fixtureInfo->id, $playerInfo->id, 8.0);
    }

    public function test_最大評価回数を超えているとき例外を投げる()
    {        
        $fixtureInfo = FixtureInfo::query()
            ->with(['playerInfos' => fn ($query) => $query->first()])
            ->first();
        
        $playerInfo = $fixtureInfo->playerInfos->first();
        
        $ratePlayer = app(RatePlayer::class);

        $ratePlayer->execute($fixtureInfo->id, $playerInfo->id, 8.0);

        // 評価数を最大にする
        $player = Player::first();
        $player->update(['rate_count' => 3]);
        
        $this->expectExceptionMessage('Rate limit exceeded.');
        
        $ratePlayer->execute($fixtureInfo->id, $playerInfo->id, 9.0);
    }
}