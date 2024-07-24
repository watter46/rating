<?php declare(strict_types=1);

namespace Tests\Unit\UseCases\Admin\Listener;

use Tests\TestCase;
use Illuminate\Support\Carbon;

use App\Models\FixtureInfo;
use App\Models\PlayerInfo;
use App\UseCases\Admin\Fixture\RegisterFixtureInfo;
use App\Http\Controllers\Util\TestLeagueImageFile;
use App\Http\Controllers\Util\TestPlayerImageFile;
use App\Http\Controllers\Util\TestPlayerInfoFile;
use App\Http\Controllers\Util\TestTeamImageFile;
use Database\Seeders\Tests\Admin\FixturePlayerInfosRemovedSeeder;


class FixtureInfoRegisteredTest extends TestCase
{
    protected $seeder = FixturePlayerInfosRemovedSeeder::class;

    public function setUp(): void
    {
        Carbon::setTestNow(Carbon::parse('2023-11-11'));

        parent::setUp();
    }

    public function test_DBに保存されている()
    {
        $this->assertDatabaseCount('fixture_infos', 1);
        $this->assertDatabaseCount('player_infos', 15);
    }
    
    public function test_出場した選手のPlayerInfoが存在しないときFlashLiveSportsからデータを取得して保存する(): void
    {
        $fixtureInfo = FixtureInfo::select('id')->first();

        /** @var RegisterFixtureInfo $registerFixtureInfo */
        $registerFixtureInfo = app(RegisterFixtureInfo::class);
        
        $registerFixtureInfo->execute($fixtureInfo->id);

        $this->assertDatabaseCount('player_infos', 16);
        
        $fixtureInfo = FixtureInfo::query()
            ->select('id')
            ->withCount('playerInfos as playerInfoCount')
            ->find($fixtureInfo->id);
            
        $this->assertSame(16, $fixtureInfo->playerInfoCount);
    }

    public function test_チームの画像が存在しないとき取得して画像をpublic下に保存できる(): void
    {
        $file = new TestTeamImageFile;

        $file->toBackup();

        $this->assertFileDoesNotExist(public_path('teams/49'));
        
        $fixtureInfo = FixtureInfo::select('id')->first();

        /** @var RegisterFixtureInfo $registerFixtureInfo */
        $registerFixtureInfo = app(RegisterFixtureInfo::class);
        
        $registerFixtureInfo->execute($fixtureInfo->id);

        $this->assertFileExists(public_path('teams/49'));

        $file->deleteBackUp();
    }

    public function test_リーグの画像が存在しないとき取得して画像をpublic下に保存できる(): void
    {
        $file = new TestLeagueImageFile;
        
        $file->toBackup();

        $this->assertFileDoesNotExist(public_path('leagues/39'));
        
        $fixtureInfo = FixtureInfo::select('id')->first();

        /** @var RegisterFixtureInfo $registerFixtureInfo */
        $registerFixtureInfo = app(RegisterFixtureInfo::class);
        
        $registerFixtureInfo->execute($fixtureInfo->id);

        $this->assertFileExists(public_path('leagues/39'));

        $file->deleteBackUp();
    }

    public function test_選手の画像が存在しないとき取得して画像をpublic下に保存する(): void
    {
        // カイセドのPlayerInfoを登録する
        $fixtureInfo = FixtureInfo::select(['id', 'external_fixture_id'])->first();

        $fixtureInfo
            ->playerInfos()
            ->saveMany(
                (new TestPlayerInfoFile)
                    ->get($fixtureInfo->external_fixture_id)
                    ->filter(fn ($player) => $player->api_football_id === 116117)
                    ->map(function ($player) {
                        return PlayerInfo::factory()
                            ->fromFile($player)
                            ->make();
                    })
            );
        
        $file = new TestPlayerImageFile;
        
        $file->toBackup();
        
        $this->assertFileDoesNotExist(public_path('images/116117'));

        /** @var RegisterFixtureInfo $registerFixtureInfo */
        $registerFixtureInfo = app(RegisterFixtureInfo::class);
        
        $registerFixtureInfo->execute($fixtureInfo->id);

        $this->assertFileExists(public_path('images/116117'));

        $file->deleteBackUp();
    }
}