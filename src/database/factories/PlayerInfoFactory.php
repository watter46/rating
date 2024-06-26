<?php declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\File;

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
                'name'           => $data->name, 
                'number'         => $data->number, 
                'season'         => $data->season, 
                'foot_player_id' => $data->foot_player_id, 
                'sofa_player_id' => $data->sofa_player_id
            ];
        });
    }

    public function toArray()
    {
        $playerInfo = $this->make();
        
        return [
            'name'           => $playerInfo->name, 
            'number'         => $playerInfo->number, 
            'season'         => $playerInfo->season, 
            'foot_player_id' => $playerInfo->foot_player_id, 
            'sofa_player_id' => $playerInfo->sofa_player_id
        ];
    }
}
