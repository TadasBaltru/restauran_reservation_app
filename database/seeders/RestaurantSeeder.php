<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RestaurantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create sample restaurants with tables
        $restaurants = [
            [
                'name' => 'The Grand Bistro',
                'tables' => [
                    ['name' => 'Table 1', 'min_people_count' => 2, 'max_people_count' => 4],
                    ['name' => 'Table 2', 'min_people_count' => 2, 'max_people_count' => 4],
                    ['name' => 'Table 3', 'min_people_count' => 4, 'max_people_count' => 6],
                    ['name' => 'Table 4', 'min_people_count' => 6, 'max_people_count' => 8],
                    ['name' => 'Booth A', 'min_people_count' => 2, 'max_people_count' => 4],
                    ['name' => 'Booth B', 'min_people_count' => 2, 'max_people_count' => 4],
                ]
            ],
            [
                'name' => 'Oceanview Restaurant',
                'tables' => [
                    ['name' => 'Patio-1', 'min_people_count' => 2, 'max_people_count' => 2],
                    ['name' => 'Patio-2', 'min_people_count' => 4, 'max_people_count' => 4],
                    ['name' => 'Patio-3', 'min_people_count' => 6, 'max_people_count' => 8],
                    ['name' => 'Indoor-A1', 'min_people_count' => 2, 'max_people_count' => 4],
                    ['name' => 'Indoor-A2', 'min_people_count' => 2, 'max_people_count' => 4],
                    ['name' => 'VIP Suite', 'min_people_count' => 8, 'max_people_count' => 12],
                ]
            ],
            [
                'name' => 'Cozy Corner Cafe',
                'tables' => [
                    ['name' => '1', 'min_people_count' => 1, 'max_people_count' => 2],
                    ['name' => '2', 'min_people_count' => 1, 'max_people_count' => 2],
                    ['name' => '3', 'min_people_count' => 2, 'max_people_count' => 3],
                    ['name' => '4', 'min_people_count' => 2, 'max_people_count' => 4],
                    ['name' => 'Counter', 'min_people_count' => 1, 'max_people_count' => 6],
                ]
            ]
        ];

        foreach ($restaurants as $restaurantData) {
            $restaurant = \App\Models\Restaurant::create([
                'name' => $restaurantData['name']
            ]);

            foreach ($restaurantData['tables'] as $tableData) {
                $restaurant->tables()->create($tableData);
            }
        }

        // Create an admin user if it doesn't exist
        $adminUser = \App\Models\User::firstOrCreate(
            ['email' => 'admin@restaurant.com'],
            [
                'name' => 'Restaurant Admin',
                'password' => bcrypt('password'),
                'is_admin' => true,
            ]
        );
    }
}
