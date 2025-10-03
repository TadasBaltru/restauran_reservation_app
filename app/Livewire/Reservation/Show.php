<?php

namespace App\Livewire\Reservation;

use Livewire\Component;
use App\Models\Reservation;
use Illuminate\View\View;

class Show extends Component
{
    public Reservation $reservation;
    public $showCancelModal = false;

    public function mount(Reservation $reservation): void
    {
        $this->reservation = $reservation->load([
            'restaurant',
            'guests',
            'tables'
        ]);
    }

    public function confirmCancel(): void
    {
        $this->showCancelModal = true;
    }

    public function cancelReservation()
    {
        try {
            $reservationId = $this->reservation->id;
            
            $this->reservation->delete();
            
            session()->flash('success', 'Reservation #' . $reservationId . ' has been cancelled successfully.');
            
            return redirect()->route('reservations.index');
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to cancel reservation: ' . $e->getMessage());
        }
    }

    public function render(): View
    {
        return view('livewire.reservation.show');
    }
}

