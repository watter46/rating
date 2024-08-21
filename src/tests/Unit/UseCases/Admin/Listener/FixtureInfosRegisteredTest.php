<?php declare(strict_types=1);

namespace Tests\Unit\UseCases\Admin\Listener;

use Tests\TestCase;
use Illuminate\Support\Carbon;

use App\Http\Controllers\Util\TestLeagueImageFile;
use App\Http\Controllers\Util\TestTeamImageFile;
use App\UseCases\Admin\Fixture\RegisterFixtureInfos;


class FixtureInfosRegisteredTest extends TestCase
{
    public function setUp(): void
    {
        Carbon::setTestNow(Carbon::parse('2023-11-11'));

        parent::setUp();
    }

    public function test_チームの画像が存在しないとき取得して画像をpublic下に保存できる(): void
    {
        $file = new TestTeamImageFile;

        $file->toBackup();

        $this->assertFileDoesNotExist(public_path('teams/49'));
        
        /** @var RegisterFixtureInfos $registerFixtureInfos */
        $registerFixtureInfos = app(RegisterFixtureInfos::class);
        
        $registerFixtureInfos->execute();

        $this->assertFileExists(public_path('teams/49'));

        $file->deleteBackUp();
    }

    public function test_リーグの画像が存在しないとき取得して画像をpublic下に保存できる(): void
    {
        $file = new TestLeagueImageFile;
        
        $file->toBackup();

        $this->assertFileDoesNotExist(public_path('leagues/39'));
        
        /** @var RegisterFixtureInfos $registerFixtureInfos */
        $registerFixtureInfos = app(RegisterFixtureInfos::class);
        
        $registerFixtureInfos->execute();

        $this->assertFileExists(public_path('leagues/39'));

        $file->deleteBackUp();
    }
}