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
                'api_football_id' => $data->api_football_id, 
                'flash_live_sports_id' => $data->flash_live_sports_id,
                'flash_live_sports_image_id' => $data->flash_live_sports_image_id
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
            'api_football_id' => $playerInfo->api_football_id, 
            'flash_live_sports_id' => $playerInfo->flash_live_sports_id,
            'flash_live_sports_image_id' => $playerInfo->flash_live_sports_image_id
        ];
    }
}
