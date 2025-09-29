<?php

namespace App\Livewire;

use Livewire\Component;

class RestaurantTableManager extends Component
{
    public $tables = [];

    public function mount($initialTables = [])
    {
        // Initialize with existing tables or start with one empty table
        $this->tables = !empty($initialTables) ? $initialTables : [
            ['id' => null, 'name' => '', 'min_people_count' => 1, 'max_people_count' => 1]
        ];
    }

    public function addTable()
    {
        $this->tables[] = [
            'id' => null,
            'name' => '',
            'min_people_count' => 1,
            'max_people_count' => 1
        ];
    }

    public function removeTable($index)
    {
        if (count($this->tables) > 1) {
            unset($this->tables[$index]);
            $this->tables = array_values($this->tables); // Re-index array
        }
    }

    public function updated($propertyName)
    {
        // Validate that max_people_count is not less than min_people_count
        if (preg_match('/tables\.(\d+)\.min_people_count/', $propertyName, $matches)) {
            $index = $matches[1];
            if ($this->tables[$index]['max_people_count'] < $this->tables[$index]['min_people_count']) {
                $this->tables[$index]['max_people_count'] = $this->tables[$index]['min_people_count'];
            }
        }
    }

    public function render()
    {
        return view('livewire.restaurant-table-manager');
    }
}
