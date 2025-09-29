<?php

namespace App\Livewire\Restaurant;

use App\Models\Restaurant;
use App\Services\RestaurantService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Livewire\Component;

class Edit extends Component
{
    public Restaurant $restaurant;
    public $name = '';
    public $tables = [];


    public function mount(Restaurant $restaurant): void
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

    public function cancel(): RedirectResponse
    {
        return redirect()->route('restaurants.index');
    }

    public function render(): View
    {
        return view('livewire.restaurant.edit');
    }
}
