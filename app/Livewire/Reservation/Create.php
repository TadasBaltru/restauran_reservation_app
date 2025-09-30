<?php

namespace App\Livewire\Reservation;

use App\Services\ReservationService;
use App\Models\Restaurant;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Livewire\Component;
use Carbon\Carbon;

class Create extends Component
{
    public $restaurant_id = '';
    public $reservation_name = '';
    public $reservation_surname = '';
    public $email = '';
    public $phone = '';
    public $reservation_date = '';
    public $reservation_time = '';
    public $duration_hours = 1;
    public $guests = [];

    public $restaurants = [];
    public $selectedRestaurant = null;

    public function mount(): void
    {
        $this->restaurants = Restaurant::with('tables')->get();

        $this->reservation_date = Carbon::today()->format('Y-m-d');

        $this->reservation_time = Carbon::now()->addHour()->format('H:00');
    }

    public function updatedRestaurantId($value): void
    {
        if ($value) {
            $this->selectedRestaurant = $this->restaurants->find($value);
        } else {
            $this->selectedRestaurant = null;
        }
    }

    public function addGuest(): void
    {
        $this->guests[] = [
            'name' => '',
            'surname' => '',
            'email' => '',
        ];
    }

    public function removeGuest($index): void
    {
        unset($this->guests[$index]);
        $this->guests = array_values($this->guests); // Re-index array
    }

    public function checkAvailability(): void
    {
        if (!$this->restaurant_id || !$this->reservation_date || !$this->reservation_time) {
            return;
        }

        $reservationService = app(ReservationService::class);
        $isAvailable = $reservationService->checkAvailability(
            $this->restaurant_id,
            $this->reservation_date,
            $this->reservation_time,
            $this->duration_hours
        );

        if (!$isAvailable) {
            $this->addError('reservation_time', 'This time slot is not available. Please choose a different time.');
        } else {
            $this->resetErrorBag('reservation_time');
            session()->flash('availability', 'This time slot is available!');
        }
    }

    public function save()
    {
        $data = [
            'restaurant_id' => $this->restaurant_id,
            'reservation_name' => $this->reservation_name,
            'reservation_surname' => $this->reservation_surname,
            'email' => $this->email,
            'phone' => $this->phone,
            'reservation_date' => $this->reservation_date,
            'reservation_time' => $this->reservation_time,
            'duration_hours' => $this->duration_hours,
            'guests' => $this->guests,
        ];

        $reservationService = app(ReservationService::class);
        $result = $reservationService->createReservation($data);

        if ($result['success']) {
            session()->flash('success', 'Reservation created successfully');
            $this->dispatch('reservation-created');

            // Reset form
            $this->reset([
                'restaurant_id', 'reservation_name', 'reservation_surname',
                'email', 'phone', 'guests'
            ]);

            // Reset to defaults
            $this->reservation_date = Carbon::today()->format('Y-m-d');
            $this->reservation_time = Carbon::now()->addHour()->format('H:00');
            $this->duration_hours = 1;
            $this->selectedRestaurant = null;

            return;
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

    public function getTimeSlots(): array
    {
        $slots = [];
        for ($hour = 8; $hour <= 22; $hour++) {
            $time = sprintf('%02d:00', $hour);
            $displayTime = Carbon::createFromFormat('H:i', $time)->format('H:i');
            $slots[$time] = $displayTime;
        }
        return $slots;
    }

    public function render(): View
    {
        return view('livewire.reservation.create', [
            'timeSlots' => $this->getTimeSlots()
        ])->layout('layouts.public');
    }
}
