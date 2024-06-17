<?php declare(strict_types=1);

namespace Database\Seeders\Tests\User;

use App\Models\FixtureInfo;
use Database\Stubs\Fixture\StubRegisterFixtureInfo;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TestingFixtureInfosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1035505 bha
        // 1035528 ham
        // 1035515 vil
        // 1035454 ars
        // 1035480 utd

        $list = collect([1035505, 1035528, 1035515, 1035454, 1035480]);

        $list
            ->each(function (int $external_fixture_id) {
                FixtureInfo::factory()
                    ->byExternalFixtureId($external_fixture_id)
                    ->create();
            });
    }
}
