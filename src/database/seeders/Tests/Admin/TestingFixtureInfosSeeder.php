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
            ->map(function (Collection $data, $index) {                
                if ($index <= 2) {
                    return FixtureInfo::factory()
                        ->fromFile($data)
                        ->nullLineup()
                        ->state(['date' => now()->subDays(1 * ($index + 1))])
                        ->toArray();
                }

                return FixtureInfo::factory()
                    ->fromFile($data)
                    ->state(['date' => now()->subDays(1 * ($index + 1))])
                    ->toArray();
            });
        
        FixtureInfo::upsert($fixtureInfos->toArray(), ['id']);
    }
}
