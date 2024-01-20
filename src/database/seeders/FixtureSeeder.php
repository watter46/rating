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
        $list = (new FixtureFile)->getIdList();

        $list->each(function ($id) {
            $this->save($id);
        });
    }

    private function save(int $fixtureId)
    {
        /** @var FixtureDataBuilder $builder */
        $builder = app(FixtureDataBuilder::class);
        
        /** @var Fixture $fixture */
        $fixture = Fixture::where('external_fixture_id', $fixtureId)->first();

        $fetched = (new FixtureFile)->get($fixtureId);

        $data = $builder->build($fetched[0]);

        $fixture->updateFixture($data)->save();
    }
}