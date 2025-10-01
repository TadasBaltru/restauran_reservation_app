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

    protected $queryString = [
        'search' => ['except' => ''],
        'restaurantFilter' => ['except' => ''],
        'dateFilter' => ['except' => '']
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


    public function getReservationsProperty()
    {
        $query = Reservation::with(['restaurant', 'guests', 'tables'])
            ->orderBy('reservation_date', 'desc');

        // Apply search filter
        if (!empty($this->search)) {
            $query->where(function ($q) {
                $q->where('reservation_name', 'like', '%' . $this->search . '%')
                  ->orWhere('reservation_surname', 'like', '%' . $this->search . '%')
                  ->orWhere('email', 'like', '%' . $this->search . '%')
                  ->orWhere('phone', 'like', '%' . $this->search . '%');
            });
        }

        // Apply restaurant filter
        if (!empty($this->restaurantFilter)) {
            $query->where('restaurant_id', $this->restaurantFilter);
        }

        // Apply date filter
        if (!empty($this->dateFilter)) {
            $query->whereDate('reservation_date', $this->dateFilter);
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

