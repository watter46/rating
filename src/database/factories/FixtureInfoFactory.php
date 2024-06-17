<?php declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\File;

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

    public function fromFile($data)
    {
        return $this->state(function (array $attributes) use ($data) {
            return [
                'external_fixture_id' => $data->external_fixture_id,
                'external_league_id'  => $data->external_league_id,
                'season'              => $data->season,
                'date'                => now('UTC'),
                'status'              => $data->status,
                'score'               => $data->score,
                'teams'               => $data->teams,
                'league'              => $data->league,
                'fixture'             => $data->fixture,
                'lineups'             => $data->lineups
            ];
        });
    }
}
