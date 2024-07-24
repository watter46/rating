<?php declare(strict_types=1);

namespace Database\Seeders\Tests\Admin;

use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;

use App\Http\Controllers\Util\TestFixtureInfoFile;
use App\Models\FixtureInfo;


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
            ->pipe(function (Collection $fixtureInfos) {
                $nullLineupsIndexes = $fixtureInfos->keys()->reverse()->take(3);
                
                return $fixtureInfos
                    ->map(function (Collection $data, $index) use ($nullLineupsIndexes) {
                        if ($nullLineupsIndexes->some(fn($i) => $i === $index)) {
                            return FixtureInfo::factory()
                                ->fromFile($data)
                                ->nullLineup()
                                ->subDays($index + 1)
                                ->make()
                                ->castsToJson();
                        }
        
                        return FixtureInfo::factory()
                            ->fromFile($data)
                            ->subDays($index + 1)
                            ->make()
                            ->castsToJson();
                    });
            });
        
        FixtureInfo::upsert($fixtureInfos->toArray(), ['id']);
    }
}
