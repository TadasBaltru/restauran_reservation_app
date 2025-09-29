<?php

namespace App\Livewire\Restaurant;

use App\Services\RestaurantService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Livewire\Component;

class Create extends Component
{
    public $name = '';
    public $tables = [];

    public function mount(): void
    {
        $this->addTable();
    }

    public function addTable(): void
    {
        $this->tables[] = [
            'id' => null,
            'name' => '',
            'min_people_count' => 1,
            'max_people_count' => 4,
        ];
    }

    public function removeTable($index): void
    {
        if (count($this->tables) > 1) {
            unset($this->tables[$index]);
            $this->tables = array_values($this->tables); // Re-index array
        }
    }

    public function save()
    {
        $data = [
            'name' => $this->name,
            'tables' => $this->tables,
        ];

        $restaurantService = app(RestaurantService::class);
        $result = $restaurantService->createRestaurant($data);

        if ($result['success']) {
            session()->flash('success', $result['message']);
            $this->dispatch('restaurant-created');
            return redirect()->route('restaurants.index');
        } else {
            if (isset($result['errors'])) {
                foreach ($result['errors']->messages() as $field => $messages) {
                    foreach ($messages as $message) {
                        $this->addError($field, $message);
                    }
                }
            } else {
                return session()->flash('error', $result['message']);
            }
        }
    }

    public function cancel(): RedirectResponse
    {
        return redirect()->route('restaurants.index');
    }

    public function render(): View
    {
        return view('livewire.restaurant.create');
    }
}
