<?php

namespace App\Livewire\Restaurant;

use App\Models\Restaurant;
use App\Services\RestaurantService;
use Livewire\Component;

class Show extends Component
{
    public Restaurant $restaurant;

    public function mount(Restaurant $restaurant)
    {
        $restaurantService = app(RestaurantService::class);
        $this->restaurant = $restaurantService->getRestaurantWithRelations(
            $restaurant->id,
            ['tables', 'reservations.guests']
        );
    }

    public function render()
    {
        return view('livewire.restaurant.show');
    }
}
