<?php

namespace App\Livewire\Restaurant;

use App\Services\RestaurantService;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $confirmingDeletion = false;
    public $restaurantToDelete = null;

    protected $listeners = [
        'restaurant-created' => 'refreshComponent',
        'restaurant-updated' => 'refreshComponent',
    ];

    public function confirmDelete($restaurantId)
    {
        $this->restaurantToDelete = $restaurantId;
        $this->confirmingDeletion = true;
    }

    public function cancelDelete()
    {
        $this->confirmingDeletion = false;
        $this->restaurantToDelete = null;
    }

    public function deleteRestaurant()
    {
        if ($this->restaurantToDelete) {
            $restaurant = \App\Models\Restaurant::find($this->restaurantToDelete);
            if ($restaurant) {
                $restaurantService = app(RestaurantService::class);
                $result = $restaurantService->deleteRestaurant($restaurant);

                if ($result['success']) {
                    session()->flash('success', $result['message']);
                } else {
                    session()->flash('error', $result['message']);
                }
            }
        }

        $this->confirmingDeletion = false;
        $this->restaurantToDelete = null;
        $this->resetPage();
    }

    public function refreshComponent()
    {
        $this->resetPage();
    }

    public function render()
    {
        $restaurantService = app(RestaurantService::class);
        return view('livewire.restaurant.index', [
            'restaurants' => $restaurantService->getPaginatedRestaurants(12)
        ]);
    }
}
