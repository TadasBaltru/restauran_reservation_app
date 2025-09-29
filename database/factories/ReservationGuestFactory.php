<?php

namespace Database\Factories;

use App\Models\ReservationGuest;
use App\Models\Reservation;
use Illuminate\Database\Eloquent\Factories\Factory;


class ReservationGuestFactory extends Factory
{
    protected $model = ReservationGuest::class;


    public function definition(): array
    {
        return [
            'reservation_id' => Reservation::factory(),
            'name' => fake()->firstName(),
            'surname' => fake()->lastName(),
            'email' => fake()->optional(0.7)->safeEmail(), // 70% chance of having email
        ];
    }


    public function withoutEmail(): static
    {
        return $this->state(fn (array $attributes) => [
            'email' => null,
        ]);
    }


    public function withEmail(): static
    {
        return $this->state(fn (array $attributes) => [
            'email' => fake()->safeEmail(),
        ]);
    }
}
