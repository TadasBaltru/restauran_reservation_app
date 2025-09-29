<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Restaurant;
use App\Models\Table;
use App\Models\Reservation;
use App\Models\ReservationGuest;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create admin user
        User::factory()->create([
            'name' => 'admin',
            'email' => 'admin@restaurant.com',
            'password' => Hash::make('super123'),
        ]);

        // Create restaurants with tables and reservations
        $restaurants = Restaurant::factory(3)->create();

        foreach ($restaurants as $restaurant) {
            // Create different types of tables for each restaurant
            $tables = collect([
                Table::factory(4)->small()->create(['restaurant_id' => $restaurant->id]),
                Table::factory(3)->medium()->create(['restaurant_id' => $restaurant->id]),
                Table::factory(2)->large()->create(['restaurant_id' => $restaurant->id]),
                Table::factory(1)->extraLarge()->create(['restaurant_id' => $restaurant->id]),
            ])->flatten();

            // Create reservations for this restaurant
            $reservations = Reservation::factory(15)->create([
                'restaurant_id' => $restaurant->id,
            ]);

            foreach ($reservations as $reservation) {
                // Randomly assign 1-3 tables to each reservation
                $selectedTables = $tables->random(rand(1, 3));
                $reservation->tables()->attach($selectedTables->pluck('id'));

                // Add guests to some reservations (1-5 guests per reservation)
                $guestCount = rand(1, 5);
                ReservationGuest::factory($guestCount)->create([
                    'reservation_id' => $reservation->id,
                ]);
            }
        }

        // Create some specific time-based reservations for demonstration
        $firstRestaurant = $restaurants->first();
        
        // Today's reservations
        $todayReservations = Reservation::factory(5)->today()->create([
            'restaurant_id' => $firstRestaurant->id,
        ]);

        // Evening reservations
        $eveningReservations = Reservation::factory(8)->evening()->create([
            'restaurant_id' => $firstRestaurant->id,
        ]);

        // Lunch reservations
        $lunchReservations = Reservation::factory(6)->lunch()->create([
            'restaurant_id' => $firstRestaurant->id,
        ]);

        // Assign tables and guests to these specific reservations as well
        foreach ([$todayReservations, $eveningReservations, $lunchReservations] as $reservationGroup) {
            foreach ($reservationGroup as $reservation) {
                $firstRestaurantTables = $firstRestaurant->tables;
                $selectedTables = $firstRestaurantTables->random(rand(1, 2));
                $reservation->tables()->attach($selectedTables->pluck('id'));

                $guestCount = rand(0, 3);
                if ($guestCount > 0) {
                    ReservationGuest::factory($guestCount)->create([
                        'reservation_id' => $reservation->id,
                    ]);
                }
            }
        }
    }
}
