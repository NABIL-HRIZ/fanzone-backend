<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Matche;
/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Zone>
 */
class ZoneFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
       return [
            'matche_id' => Matche::factory(), 
            'name' => $this->faker->word . ' Zone',
            'city' => $this->faker->city,
            'address' => $this->faker->address,
            'latitude' => $this->faker->latitude,
            'longitude' => $this->faker->longitude,
            'capacity' => $this->faker->numberBetween(50, 500),
            'available_seats' => $this->faker->numberBetween(0, 50),
            'type' => $this->faker->randomElement(['vip', 'standard', 'famille']),
            'description' => $this->faker->sentence(),
            'image' => $this->faker->imageUrl(640, 480, 'sports', true),
        ];
    }
}
