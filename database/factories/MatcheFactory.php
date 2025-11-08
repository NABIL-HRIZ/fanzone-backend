<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Matche>
 */
class MatcheFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
       return [
            'team_one_title' => $this->faker->city . ' Royaume',
            'team_one_image' =>  $this->faker->imageUrl(100, 100),
            'team_two_title' => $this->faker->city . ' United',
            'team_two_image' => $this->faker->imageUrl(100, 100),
            'match_date' => $this->faker->dateTimeBetween('+1 days', '+2 months'),
            'stadium' => $this->faker->city . ' Stadium',
            'description' => $this->faker->sentence(),
        ];
    }
}
