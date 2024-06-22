<?php declare(strict_types=1);

namespace Database\Seeders\Tests\User;

use Illuminate\Database\Seeder;

use App\Http\Controllers\Util\TestFixtureInfoFile;
use App\Http\Controllers\Util\TestOneItemFile;
use App\Models\Average;
use App\Models\Fixture;
use App\Models\FixtureInfo;
use App\Models\Player;
use App\Models\PlayerInfo;
use Database\Factories\UsersRatingFactory;
use Illuminate\Support\Carbon;

class TestingFixtureSeeder extends Seeder
{
    /** vs Man United */
    private const EXTERNAL_FIXTURE_ID = 1035480;
    
    /** vs Arsenal vs Man United vs Man City */
    private const EXTERNAL_FIXTURE_ID_LIST = [1035454, 1035480, 1035151];

    /** Caicedo */
    private const FOOT_PLAYER_ID = 116117;
    
    public function run(): void
    {   
        $testFixtureInfos = new TestFixtureInfoFile();

        $testFixtureInfos
            ->gets(self::EXTERNAL_FIXTURE_ID_LIST)
            ->each(function ($data, $i) {
                $dates = [
                    Carbon::now('UTC')->subDays(5),
                    Carbon::now('UTC'),
                    Carbon::now('UTC')->addDays(5)
                ];
                
                FixtureInfo::factory()
                    ->fromFile($data)
                    ->create([
                        'date' => $dates[$i]
                    ]);
            });

        $fixtureInfo = FixtureInfo::query()
            ->where('external_fixture_id', self::EXTERNAL_FIXTURE_ID)
            ->first();
            
        $testData = new TestOneItemFile;

        $playerInfo = $fixtureInfo
            ->playerInfos()
            ->save(
                PlayerInfo::factory()
                    ->fromFile($testData->getPlayerInfo(self::FOOT_PLAYER_ID))
                    ->make()
            );

        Average::factory()
            ->create([
                'fixture_info_id' => $fixtureInfo->id,
                'player_info_id'  => $playerInfo->id
            ]);
        
        $fixture = $fixtureInfo
            ->refresh()
            ->fixture()
            ->save(
                Fixture::factory()
                    ->fromFile($testData->getFixture())
                    ->make()
            );
            
        $fixture
            ->refresh()
            ->players()
            ->save(
                Player::factory()
                    ->fromFile($testData->getPlayer())
                    ->make([
                        'player_info_id' => $playerInfo->refresh()->id
                    ])
            );
    }
}
