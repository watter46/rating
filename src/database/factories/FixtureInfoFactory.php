<?php declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Collection;

use App\UseCases\Admin\Data\ApiFootball\FixtureData\FixtureStatusType;
use App\UseCases\Util\Season;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\FixtureInfo>
 */
class FixtureInfoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            //
        ];
    }

    public function fromFile(Collection $data)
    {
        return $this->state(function (array $attributes) use ($data) {
            return [
                'api_fixture_id' => $data['api_fixture_id'],
                'api_league_id'  => $data['api_league_id'],
                'season'         => $data['season'],
                'date'           => $data['date'],
                'is_end'         => $data['is_end'],
                'score'          => collect($data['score']),
                'teams'          => collect($data['teams']),
                'league'         => collect($data['league']),
                'fixture'        => collect($data['fixture']),
                'lineups'        => collect($data['lineups'])
            ];
        });
    }

    public function fromFileToArray(Collection $data): array
    {
        return [
            'api_fixture_id' => $data['api_fixture_id'],
            'api_league_id'  => $data['api_league_id'],
            'season'         => $data['season'],
            'date'           => $data['date'],
            'is_end'         => $data['is_end'],
            'score'          => collect($data['score']),
            'teams'          => collect($data['teams']),
            'league'         => collect($data['league']),
            'fixture'        => collect($data['fixture']),
            'lineups'        => collect($data['lineups'])
        ];
    }

    public function nowDate()
    {
        return $this->state(function (array $attributes) {
            return [
                'season' => Season::current(),
                'date' => now('UTC')
            ];
        });
    }

    public function notStarted()
    {
        return $this->state(function (array $attributes) {
            return [
                'is_end'  => false,
                'score'   => $attributes['score']
                    ->dataSet('fulltime.away', null)
                    ->dataSet('fulltime.home', null),
                'fixture' => $attributes['fixture']
                    ->dataSet('is_end', false)
                    ->dataSet('winner', null),
                'lineups' => null
            ];
        });
    }

    public function nullLineup()
    {
        return $this->state(function (array $attributes) {
            return [
                'lineups' => null
            ];
        });
    }

    public function subDays(int $days)
    {
        return $this->state(function (array $attributes) use ($days) {
            return ['date' => now()->subDays($days)];
        });
    }
}
