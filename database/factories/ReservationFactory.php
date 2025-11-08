<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use App\Models\Zone;


/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Reservation>
 */
class ReservationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */

    protected $model = \App\Models\Reservation::class;
   
     public function definition(): array
    {
         $numberOfTickets = $this->faker->numberBetween(1, 5);
      
            return [
           'user_id' => User::inRandomOrder()->first()->id,       
           'zone_id' => Zone::inRandomOrder()->first()->id,      
            'number_of_tickets' => $numberOfTickets,
            'total_price' => $numberOfTickets * $this->faker->randomFloat(2, 10, 100), 
            'payment_status' => $this->faker->randomElement(['unpaid', 'paid', 'simulated']),
            'qr_code_path' => null, 
            'ticket_pdf_path' => null,
            'reservation_date' => $this->faker->dateTimeBetween('-1 month', '+1 month'),
            'stripe_payment_intent_id' => null,
            'stripe_session_id' => null,
        ];
}
}
