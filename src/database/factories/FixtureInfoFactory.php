<?php declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Collection;

use App\UseCases\Admin\Fixture\Data\FixtureStatusType;

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
                'external_fixture_id' => $data['external_fixture_id'],
                'external_league_id'  => $data['external_league_id'],
                'season'              => $data['season'],
                'date'                => now('UTC'),
                'status'              => $data['status'],
                'score'               => collect($data['score']),
                'teams'               => collect($data['teams']),
                'league'              => collect($data['league']),
                'fixture'             => collect($data['fixture']),
                'lineups'             => collect($data['lineups'])
            ];
        });
    }

    public function fromFileToArray(Collection $data): array
    {
        return [
            'external_fixture_id' => $data['external_fixture_id'],
            'external_league_id'  => $data['external_league_id'],
            'season'              => $data['season'],
            'date'                => now('UTC'),
            'status'              => $data['status'],
            'score'               => collect($data['score']),
            'teams'               => collect($data['teams']),
            'league'              => collect($data['league']),
            'fixture'             => collect($data['fixture']),
            'lineups'             => collect($data['lineups'])
        ];
    }

    public function notStarted()
    {
        return $this->state(function (array $attributes) {
            return [
                'status'  => FixtureStatusType::NotStarted->value,
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

    public function toArray()
    {
        $fixtureInfo = $this->make();
        
        return [
            'external_fixture_id' => $fixtureInfo->external_fixture_id,
            'external_league_id'  => $fixtureInfo->external_league_id,
            'season'              => $fixtureInfo->season,
            'date'                => $fixtureInfo->date,
            'status'              => $fixtureInfo->status,
            'score'               => $fixtureInfo->score,
            'teams'               => $fixtureInfo->teams,
            'league'              => $fixtureInfo->league,
            'fixture'             => $fixtureInfo->fixture,
            'lineups'             => $fixtureInfo->lineups
        ];
    }
}
