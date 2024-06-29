<?php declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PlayerInfo>
 */
class PlayerInfoFactory extends Factory
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
                'name' => $data->name, 
                'number' => $data->number, 
                'season' => $data->season, 
                'foot_ball_api_id' => $data->foot_ball_api_id, 
                'sofa_score_id' => $data->sofa_score_id,
                'flash_live_sports_id' => $data->flash_live_sports_id,
            ];
        });
    }

    public function toArray()
    {
        $playerInfo = $this->make();
        
        return [
            'name' => $playerInfo->name, 
            'number' => $playerInfo->number, 
            'season' => $playerInfo->season, 
            'foot_ball_api_id' => $playerInfo->foot_ball_api_id, 
            'sofa_score_id' => $playerInfo->sofa_score_id,
            'flash_live_sports_id' => $playerInfo->flash_live_sports_id
        ];
    }
}
