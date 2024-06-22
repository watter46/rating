<?php declare(strict_types=1);

namespace Tests\Unit\UseCases\User\Player;

use Tests\TestCase;

use App\Models\Fixture;
use App\Models\FixtureInfo;
use App\UseCases\User\Player\DecideManOfTheMatch;
use Database\Seeders\Tests\User\TestingFixtureInfoSeeder;


class DecideManOfTheMatchTest extends TestCase
{
    protected $seeder = TestingFixtureInfoSeeder::class;

    public function test_未評価状態からMOMを決めて正常な値が返る(): void
    {
        $fixtureInfo = FixtureInfo::query()
            ->with(['playerInfos' => fn ($query) => $query->first()])
            ->first();
        
        $playerInfo = $fixtureInfo->playerInfos->first();
        
        $decideMomTheMatch = app(DecideManOfTheMatch::class);

        $players = $decideMomTheMatch->execute($fixtureInfo->id, $playerInfo->id);
        
        $this->assertDatabaseHas('fixtures', [
                'mom_count' => 1
            ]);
        
        $this->assertDatabaseHas('players', [
                'mom' => true
            ]);
        
        // newMomPlayer
        $this->assertTrue($players[0]->mom);
        $this->assertNull($players[0]->rating);
        $this->assertSame(0, $players[0]->rate_count);
        $this->assertTrue($players[0]->canRate);
        $this->assertSame(3, $players[0]->rateLimit);

        // oldMomPlayer
        $this->assertNull($players[1]);
    }

    public function test_すでに評価している状態からMOMを決めて正常な値が返る(): void
    {
        $fixtureInfo = FixtureInfo::query()
            ->with(['playerInfos' => fn ($query) => $query->take(2)])
            ->first();
        
        $playerInfo = $fixtureInfo->playerInfos->first();
        
        $decideMomTheMatch = app(DecideManOfTheMatch::class);

        $decideMomTheMatch->execute($fixtureInfo->id, $playerInfo->id);

        $newMom = $fixtureInfo->playerInfos[1];

        $players = $decideMomTheMatch->execute($fixtureInfo->id, $newMom->id);
        
        $this->assertDatabaseHas('fixtures', [
                'mom_count' => 2
            ]);
        
        // newMomPlayer
        $this->assertTrue($players[0]->mom);
        $this->assertNull($players[0]->rating);
        $this->assertSame(0, $players[0]->rate_count);
        $this->assertTrue($players[0]->canRate);
        $this->assertSame(3, $players[0]->rateLimit);

        // oldMomPlayer
        $this->assertFalse($players[1]->mom);
        $this->assertNull($players[1]->rating);
        $this->assertSame(0, $players[1]->rate_count);
        $this->assertTrue($players[1]->canRate);
        $this->assertSame(3, $players[1]->rateLimit);
    }

    public function test_評価期間外の時に例外を投げる()
    {
        // 試合日程を6日前にする
        $fixtureInfo = FixtureInfo::first();
        $fixtureInfo->update(['date' => now('UTC')->subDays(6)]);

        $fixtureInfo = FixtureInfo::query()
            ->with(['playerInfos' => fn ($query) => $query->first()])
            ->first();
        
        $playerInfo = $fixtureInfo->playerInfos->first();
    
        $decideMomTheMatch = app(DecideManOfTheMatch::class);

        $this->expectExceptionMessage('Rate period has expired.');
        
        $decideMomTheMatch->execute($fixtureInfo->id, $playerInfo->id);
    }

    public function test_最大MOM評価回数を超えているとき例外を投げる()
    {
        $fixtureInfo = FixtureInfo::query()
            ->with(['playerInfos' => fn ($query) => $query->take(2)])
            ->first();
        
        $playerInfo = $fixtureInfo->playerInfos->first();

        $decideMomTheMatch = app(DecideManOfTheMatch::class);
        
        $decideMomTheMatch->execute($fixtureInfo->id, $playerInfo->id);

        // 評価数を最大にする
        $fixture = Fixture::first();
        $fixture->update(['mom_count' => 5]);
        
        $newMom = $fixtureInfo->playerInfos[1];

        $this->expectExceptionMessage('MOM limit exceeded.');

        $decideMomTheMatch->execute($fixtureInfo->id, $newMom->id);
    }
}
