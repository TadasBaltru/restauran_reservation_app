<?php

namespace App\Livewire\Restaurant;

use App\Models\Restaurant;
use App\Services\RestaurantService;
use Livewire\Component;

class Edit extends Component
{
    public Restaurant $restaurant;
    public $name = '';
    public $tables = [];

    protected $listeners = [
        'addTable' => 'addTable',
        'removeTable' => 'removeTable',
    ];

    public function mount(Restaurant $restaurant)
    {
        $this->restaurant = $restaurant;
        $this->name = $restaurant->name;
        $this->tables = $restaurant->tables->map(function ($table) {
            return [
                'id' => $table->id,
                'name' => $table->name,
                'min_people_count' => $table->min_people_count,
                'max_people_count' => $table->max_people_count,
            ];
        })->toArray();

        if (empty($this->tables)) {
            $this->addTable();
        }
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
            $this->tables = array_values($this->tables);
        }
    }
    public function save()
    {
        $data = [
            'name' => $this->name,
            'tables' => $this->tables,
        ];

        $restaurantService = app(RestaurantService::class);
        $result = $restaurantService->updateRestaurant($this->restaurant, $data);

        if ($result['success']) {
            session()->flash('success', $result['message']);
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
        return view('livewire.restaurant.edit');
    }
}
