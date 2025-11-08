<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Reservation;
use App\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Scan>
 */
class ScanFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
           'agent_id' => User::inRandomOrder()->first()->id,                
            'ticket_id' => Reservation::inRandomOrder()->first()->id,
            'scan_time' => $this->faker->dateTimeBetween('-1 month', 'now'),
            'scan_status' => $this->faker->randomElement(['valid', 'invalid']),
        ];
    }
}
