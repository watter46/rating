<?php declare(strict_types=1);

namespace App\UseCases\Admin\Player\UpdateRatingAverage;

use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Collection as EqCollection;

use App\Models\Average;
use App\Models\Fixture;
use App\Models\Player;

class UpdateRatingAverageBuilder
{    
    /**
     * build
     *
     * @param  string $fixtureInfoId
     * @param  EqCollection<Average> $averages
     * @param  EqCollection<Fixture> $fixtures
     * @return Collection
     */
    public function build(string $fixtureInfoId, EqCollection $averages, EqCollection $fixtures)
    {
        $averageRatings = $this->calculateAverageRatings($fixtures);
        
        return $this->mergeAverageIds($averages, $this->format($fixtureInfoId, $averageRatings));
    }
    
    /**
     * 評価されている選手のみのレーティングを取得する
     *
     * @param  EqCollection $fixtures
     * @return Collection
     */
    private function calculateAverageRatings(EqCollection $fixtures): Collection
    {
        return collect($fixtures)
            ->map(fn (Fixture $fixture) => $fixture->players
                ->map(fn (Player $player) => collect($player)->except('fixture_id'))
                ->filter(fn (Collection $stats) => $stats['rating'])
                ->values()
            )
            ->flatten(1)
            ->groupBy('player_info_id')
            ->map(function (Collection $playerStats, string $player_info_id) {
                return collect([
                    'player_info_id' => $player_info_id,
                    'momPercent' => $playerStats->percentage(fn($data) => $data['mom']),
                    'rating' => round($playerStats->avg('rating'), 1)
                ]);
            });
    }

    private function format(string $fixtureInfoId, Collection $averageRatings): Collection
    {
        return $averageRatings
            ->map(fn (Collection $players) => $players
                ->except('momPercent')
                ->put('fixture_info_id', $fixtureInfoId)
            )
            ->map(function (Collection $players) use ($averageRatings) {
                if ($players['player_info_id'] === $this->getMostMomPlayerInfoId($averageRatings)) {
                    return $players->put('mom', true);
                }

                return $players->put('mom', false);
            })
            ->values();
    }

    private function getMostMomPlayerInfoId(Collection $players): ?string
    {
        $maxMomPercent = $players->max('momPercent');

        if ($maxMomPercent === 0.0) {
            return null;
        }

        return $players
            ->where('momPercent', $players->max('momPercent'))
            ->pipe(function (Collection $momPlayers) {
                return $momPlayers->firstWhere('rating', $momPlayers->max('rating'));
            })
            ->get('player_info_id');
    }

    private function mergeAverageIds(EqCollection $averages, Collection $averageRatings): Collection
    {
        if ($averages->isEmpty()) {
            return $averageRatings;
        }

        $averageIds = $averages->mapWithKeys(fn(Average $average) => [$average->player_info_id => $average->id]);

        return $averageRatings
            ->map(function (Collection $stats) use ($averageIds) {
                $averageId = $averageIds->get($stats['player_info_id']);

                if (!$averageId) {
                    return $stats;
                }

                return $stats->merge(['id' => $averageId]);
            });
    }
}