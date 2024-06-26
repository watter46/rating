<?php declare(strict_types=1);

namespace Database\Seeders\Tests\User;

use Illuminate\Database\Seeder;

use App\Models\Fixture;
use App\Models\FixtureInfo;
use App\Models\Player;
use App\Models\PlayerInfo;
use App\Http\Controllers\Util\TestFixtureInfoFile;
use App\Http\Controllers\Util\TestOneItemFile;
use Illuminate\Support\Collection;

class TestingFixtureInfosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $testFixtureInfos = new TestFixtureInfoFile();

        $fixtureInfos = $testFixtureInfos
            ->getAll()
            ->map(function (Collection $data) {
                return FixtureInfo::factory()->fromFileToArray($data);
            });
        
        FixtureInfo::upsert($fixtureInfos->toArray(), ['id']);
        
        // // external_fixture_id: 1035480 utd
        // // player: Caicedo '01j0jwemj3gqjy4abpz5zv13hv' foot_player_id: 116117

        $fixtureInfo = $fixtureInfos->first(function ($fixtureInfo) {
                return $fixtureInfo['external_fixture_id'] === 1035480;
            });

        $testData = new TestOneItemFile;

        /** @var FixtureInfo $fixtureInfo */
        $fixtureInfo = FixtureInfo::query()
            ->where('external_fixture_id', 1035480)
            ->first();

        $playerInfo = $fixtureInfo
            ->playerInfos()
            ->save(
                PlayerInfo::factory()
                    ->fromFile($testData->getPlayerInfo(116117))
                    ->make()
            );
        
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
