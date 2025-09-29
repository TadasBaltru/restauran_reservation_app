<?php

namespace Database\Factories;

use App\Models\Table;
use App\Models\Restaurant;
use Illuminate\Database\Eloquent\Factories\Factory;

class TableFactory extends Factory
{
    protected $model = Table::class;

    public function definition(): array
    {
        $tableConfigs = [
            ['min' => 2, 'max' => 4], 
            ['min' => 4, 'max' => 6],  
            ['min' => 6, 'max' => 8],  
            ['min' => 8, 'max' => 12], 
        ];

        $config = fake()->randomElement($tableConfigs);

        return [
            'restaurant_id' => Restaurant::factory(),
            'name' => fake()->randomElement(['Table', 'T', '#']) . ' ' . fake()->numberBetween(1, 50),
            'min_people_count' => $config['min'],
            'max_people_count' => $config['max'],
        ];
    }


    public function small(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => fake()->randomElement(['Table', 'T', '#']) . ' ' . fake()->numberBetween(1, 20),
            'min_people_count' => 2,
            'max_people_count' => 4,
        ]);
    }


    public function medium(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => fake()->randomElement(['Table', 'T', '#']) . ' ' . fake()->numberBetween(21, 35),
            'min_people_count' => 4,
            'max_people_count' => 6,
        ]);
    }


    public function large(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => fake()->randomElement(['Table', 'T', '#']) . ' ' . fake()->numberBetween(36, 45),
            'min_people_count' => 6,
            'max_people_count' => 8,
        ]);
    }


    public function extraLarge(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => fake()->randomElement(['Table', 'T', '#']) . ' ' . fake()->numberBetween(46, 50),
            'min_people_count' => 8,
            'max_people_count' => 12,
        ]);
    }
}
