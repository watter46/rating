<?php declare(strict_types=1);

namespace Database\Seeders;

use App\Http\Controllers\Util\FixturesFile;
use App\Models\Fixture;
use App\UseCases\Player\Builder\FixtureDataListBuilder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FixtureListSeeder extends Seeder
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
    }
}
