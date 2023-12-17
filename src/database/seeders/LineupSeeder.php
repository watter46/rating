<?php declare(strict_types=1);

namespace Database\Seeders;

use App\Http\Controllers\Util\LineupFile;
use App\Models\Lineup;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;


class LineupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $fixtureId = 1035338;
        
        /** @var LineupFile $file */
        $file = app(LineupFile::class);

        $data = $file->get($fixtureId);

        $lineup = (new Lineup)
            ->setLineup(
                fixture_id: $fixtureId,
                lineup: json_encode($data[0]->startXI)
            );

        $lineup->save();
    }
}
