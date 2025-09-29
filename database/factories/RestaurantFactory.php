<?php

namespace Database\Factories;

use App\Models\Restaurant;
use Illuminate\Database\Eloquent\Factories\Factory;


class RestaurantFactory extends Factory
{
    protected $model = Restaurant::class;


    public function definition(): array
    {
        $restaurantTypes = [
            'Italian Bistro',
            'French Brasserie', 
            'Steakhouse',
            'Seafood Grill',
            'Mediterranean Kitchen',
            'Asian Fusion',
            'Mexican Cantina',
            'American Diner',
            'Wine Bar',
            'Tapas Bar'
        ];

        $restaurantNames = [
            'La Bella Vista',
            'The Golden Fork',
            'Sunset Terrace',
            'Ocean View Restaurant',
            'Mountain Peak Dining',
            'The Rustic Table',
            'Urban Kitchen',
            'Garden Café',
            'Riverside Grill',
            'The Corner Bistro'
        ];

        return [
            'name' => fake()->randomElement($restaurantNames) . ' ' . fake()->randomElement($restaurantTypes),
        ];
    }
}


