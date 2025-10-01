<?php

namespace App\Livewire\Reservation;

use Livewire\Component;
use App\Models\Reservation;
use Illuminate\View\View;

class Show extends Component
{
    public Reservation $reservation;

    public function mount(Reservation $reservation): void
    {
        $this->reservation = $reservation->load([
            'restaurant',
            'guests',
            'tables'
        ]);
    }

    public function render(): View
    {
        return view('livewire.reservation.show');
    }
}

