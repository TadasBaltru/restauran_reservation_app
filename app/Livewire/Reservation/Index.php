<?php

namespace App\Livewire\Reservation;

use App\Models\Reservation;
use App\Models\Restaurant;
use Illuminate\View\View;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $restaurantFilter = '';
    public $dateFilter = '';
    public $perPage = 15;
    public $sortField = 'reservation_date';
    public $sortDirection = 'desc';

    protected $queryString = [
        'search' => ['except' => ''],
        'restaurantFilter' => ['except' => ''],
        'dateFilter' => ['except' => ''],
        'sortField' => ['except' => 'reservation_date'],
        'sortDirection' => ['except' => 'desc']
    ];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingRestaurantFilter(): void
    {
        $this->resetPage();
    }

    public function updatingDateFilter(): void
    {
        $this->resetPage();
    }

    public function sortBy($field): void
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function getReservationsProperty()
    {
        $query = Reservation::with(['restaurant', 'guests', 'tables']);

        // Apply search filter
        if (!empty($this->search)) {
            $query->whereAny(
                ['reservation_name', 'reservation_surname', 'email', 'phone'],
                'like',
                "%{$this->search}%"
            );
        }

        // Apply restaurant filter
        if (!empty($this->restaurantFilter)) {
            $query->where('restaurant_id', $this->restaurantFilter);
        }

        // Apply date filter
        if (!empty($this->dateFilter)) {
            $query->whereDate('reservation_date', $this->dateFilter);
        }

        // Apply sorting
        if ($this->sortField === 'restaurant') {
            $query->join('restaurants', 'reservations.restaurant_id', '=', 'restaurants.id')
                  ->orderBy('restaurants.name', $this->sortDirection)
                  ->select('reservations.*');
        } else {
            $query->orderBy($this->sortField, $this->sortDirection);
        }

        return $query->paginate($this->perPage);
    }

    public function getRestaurantsProperty()
    {
        return Restaurant::orderBy('name')->get();
    }


    public function clearFilters(): void
    {
        $this->search = '';
        $this->restaurantFilter = '';
        $this->dateFilter = '';
        $this->resetPage();
    }

    public function render(): View
    {
        return view('livewire.reservation.index', [
            'reservations' => $this->reservations,
            'restaurants' => $this->restaurants
        ]);
    }
}







