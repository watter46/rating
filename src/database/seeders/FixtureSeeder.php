<?php declare(strict_types=1);

namespace Database\Seeders;

use App\Http\Controllers\Util\FixtureFile;
use App\Models\Fixture;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Http\Controllers\Util\FixturesFile;
use App\Http\Controllers\Util\TeamImageFile;
use App\UseCases\Player\Builder\FixtureDataBuilder;
use App\UseCases\Player\Builder\FixtureDataListBuilder;

class FixtureSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        /** @var FixturesFile $file */
        $file = app(FixturesFile::class);

        $fetched = $file->get();

        /** @var FixtureDataListBuilder $fixtureDataList */
        $fixtureDataList = app(FixtureDataListBuilder::class);

        $data = $fixtureDataList->build($fetched, []);
        
        $unique = ['id'];
        $updateColumns = ['date', 'is_end'];

        (new Fixture)->upsert($data, $unique, $updateColumns);

        $fixtureId = 1035338;
        
        /** @var Fixture $fixture */
        $fixture = Fixture::where('external_fixture_id', $fixtureId)->first();

        $fetched = (new FixtureFile)->get($fixtureId);

        $data = (new FixtureDataBuilder(new TeamImageFile))->build($fetched[0]);

        $fixture->updateFixture($data)->save();
    }
}
