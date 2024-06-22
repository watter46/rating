<?php declare(strict_types=1);

namespace Database\Factories;

use App\Models\Average;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class UsersRatingFactory extends Factory
{
    protected $model = Average::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'rating' => 10,
            'mom' => true
        ];
    }
}
