<?php

namespace Database\Factories;

use App\Models\Reservation;
use App\Models\Restaurant;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Reservation>
 */
class ReservationFactory extends Factory
{
    protected $model = Reservation::class;


    public function definition(): array
    {
        $reservationDate = fake()->dateTimeBetween('now', '+30 days');
        
        return [
            'restaurant_id' => Restaurant::factory(),
            'reservation_name' => fake()->firstName(),
            'reservation_surname' => fake()->lastName(),
            'email' => fake()->safeEmail(),
            'phone' => fake()->phoneNumber(),
            'reservation_date' => $reservationDate,
            'duration_hours' => fake()->randomElement([1, 2, 3, 4]),
        ];
    }


    public function today(): static
    {
        return $this->state(fn (array $attributes) => [
            'reservation_date' => fake()->dateTimeBetween('today', 'today +23 hours'),
        ]);
    }


    public function tomorrow(): static
    {
        return $this->state(fn (array $attributes) => [
            'reservation_date' => fake()->dateTimeBetween('tomorrow', 'tomorrow +23 hours'),
        ]);
    }

    /**
     * Create a reservation for this weekend
     */
    public function weekend(): static
    {
        return $this->state(fn (array $attributes) => [
            'reservation_date' => fake()->dateTimeBetween('next Saturday', 'next Sunday +23 hours'),
        ]);
    }

    /**
     * Create an evening reservation (6 PM - 10 PM)
     */
    public function evening(): static
    {
        return $this->state(function (array $attributes) {
            $date = fake()->dateTimeBetween('now', '+30 days');
            $hour = fake()->numberBetween(18, 22); // 6 PM to 10 PM
            $date->setTime($hour, fake()->randomElement([0, 15, 30, 45]));
            
            return [
                'reservation_date' => $date,
            ];
        });
    }

    /**
     * Create a lunch reservation (11 AM - 3 PM)
     */
    public function lunch(): static
    {
        return $this->state(function (array $attributes) {
            $date = fake()->dateTimeBetween('now', '+30 days');
            $hour = fake()->numberBetween(11, 15); // 11 AM to 3 PM
            $date->setTime($hour, fake()->randomElement([0, 15, 30, 45]));
            
            return [
                'reservation_date' => $date,
                'duration_hours' => fake()->randomElement([1, 2]),
            ];
        });
    }

    /**
     * Create a long duration reservation
     */
    public function longDuration(): static
    {
        return $this->state(fn (array $attributes) => [
            'duration_hours' => fake()->randomElement([3, 4, 5]),
        ]);
    }
}
