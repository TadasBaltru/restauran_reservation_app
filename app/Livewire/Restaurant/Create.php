<?php

namespace App\Livewire\Restaurant;

use App\Services\RestaurantService;
use Livewire\Component;

class Create extends Component
{
    public $name = '';
    public $tables = [];

    protected $listeners = [
        'addTable' => 'addTable',
        'removeTable' => 'removeTable',
    ];

    public function mount()
    {
        $this->addTable();
    }

    public function addTable()
    {
        $this->tables[] = [
            'id' => null,
            'name' => '',
            'min_people_count' => 1,
            'max_people_count' => 4,
        ];
    }

    public function removeTable($index)
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
                session()->flash('error', $result['message']);
            }
        }
    }

    public function cancel()
    {
        return redirect()->route('restaurants.index');
    }

    public function render()
    {
        return view('livewire.restaurant.create');
    }
}
