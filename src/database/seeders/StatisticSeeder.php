<?php declare(strict_types=1);

namespace Database\Seeders;

use App\Http\Controllers\Util\StatisticsFile;
use App\Models\Statistic;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StatisticSeeder extends Seeder
{
    const CHELSEA_TEAM_ID = 49;
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        /** @var StatisticsFile $file */
        $file = app(StatisticsFile::class);

        $fixtureId = 1035338;
        
        $data = $file->get($fixtureId);

        $statisticJson = collect($data);

        $team = $statisticJson->sole(fn ($teams) => $teams->team->id === self::CHELSEA_TEAM_ID);
        
        $data = collect($team->players)
            ->map(function ($player) {
                return [
                    'id'     => $player->player->id,
                    'rating' => $player->statistics[0]->games->rating
                ];
            });

        $statistic = (new Statistic)
            ->setStatistic(
                fixture_id: $fixtureId,
                statistic: json_encode($data)
            );

        $statistic->save();
    }
}
