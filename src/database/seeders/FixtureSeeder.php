<?php declare(strict_types=1);

namespace Database\Seeders;

use App\Http\Controllers\Util\FixtureFile;
use App\Models\Fixture;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\UseCases\Player\Builder\FixtureDataBuilder;

class FixtureSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {   
        $this->save(1035338);
        $this->save(1035359);
    }

    private function save(int $fixtureId)
    {
        /** @var FixtureDataBuilder $builder */
        $builder = app(FixtureDataBuilder::class);
        
        /** @var Fixture $fixture2 */
        $fixture2 = Fixture::where('external_fixture_id', $fixtureId)->first();

        $fetched2 = (new FixtureFile)->get($fixtureId);

        $data2 = $builder->build($fetched2[0]);

        $fixture2->updateFixture($data2)->save();
    }
}
