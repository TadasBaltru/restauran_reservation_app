<?php

namespace App\Services;

use App\Models\Reservation;
use App\Models\Restaurant;
use App\Models\ReservationGuest;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Illuminate\Validation\Validator as ValidationValidator;

class ReservationService
{
    public function getAllRestaurants(): Collection
    {
        return Restaurant::withCount('tables')
            ->with('tables')
            ->get();
    }

    public function createReservation(array $data): array
    {
        $validator = $this->validateReservationData($data);

        if ($validator->fails()) {
            return [
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ];
        }

        try {
            DB::beginTransaction();

            // Calculate total people (main person + guests)
            $guestCount = 0;
            if (!empty($data['guests']) && is_array($data['guests'])) {
                $guestCount = count(array_filter($data['guests'], function ($guest) {
                    return !empty($guest['name']);
                }));
            }
            $totalPeople = 1 + $guestCount; // Main person + guests

            // Attempt to allocate tables
            $tableAllocationService = app(TableAllocationService::class);
            $startTime = Carbon::parse($data['reservation_date'] . ' ' . $data['reservation_time']);

            $allocation = $tableAllocationService->allocateTables(
                $data['restaurant_id'],
                $totalPeople,
                $startTime,
                $data['duration_hours']
            );

            if (!$allocation) {
                DB::rollBack(); // Ensure transaction is rolled back
                return [
                    'success' => false,
                    'message' => 'No available tables found for your party size and requested time. Please try a different time or contact the restaurant.',
                    'error' => 'table_allocation_failed'
                ];
            }

            // Create the reservation
            $reservation = Reservation::create([
                'restaurant_id' => $data['restaurant_id'],
                'reservation_name' => $data['reservation_name'],
                'reservation_surname' => $data['reservation_surname'],
                'email' => $data['email'],
                'phone' => $data['phone'] ?? '',
                'reservation_date' => $startTime,
                'duration_hours' => $data['duration_hours'],
            ]);

            // Attach allocated tables to reservation
            $tableIds = array_column($allocation['tables'], 'table_id');
            $reservation->tables()->attach($tableIds);

            // Add guests if provided
            if (!empty($data['guests']) && is_array($data['guests'])) {
                foreach ($data['guests'] as $guestData) {
                    if (!empty($guestData['name'])) {
                        $reservation->guests()->create([
                            'name' => $guestData['name'],
                            'surname' => $guestData['surname'] ?? '',
                            'email' => $guestData['email'] ?? null,
                        ]);
                    }
                }
            }

            DB::commit();

            return [
                'success' => true,
                'reservation' => $reservation->load(['restaurant', 'guests', 'tables']),
                'allocation' => $allocation
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            return [
                'success' => false,
                'message' => 'Failed to create reservation. Please try again.',
                'error' => $e->getMessage()
            ];
        }
    }

    public function checkAvailability(int $restaurantId, string $date, string $time, int $durationHours, int $totalPeople = 1): bool
    {
        $reservationDateTime = Carbon::parse($date . ' ' . $time);

        // Use the same table allocation logic as reservation creation
        $tableAllocationService = app(TableAllocationService::class);
        $allocation = $tableAllocationService->allocateTables(
            $restaurantId,
            $totalPeople,
            $reservationDateTime,
            $durationHours
        );

        // Return true if tables can be allocated, false otherwise
        return $allocation !== null;
    }

    private function validateReservationData(array $data): ValidationValidator
    {
        $rules = [
            'restaurant_id' => 'required|exists:restaurants,id',
            'reservation_name' => 'required|string|max:255',
            'reservation_surname' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'reservation_date' => 'required|date|after_or_equal:today',
            'reservation_time' => 'required|date_format:H:i',
            'duration_hours' => 'required|integer|min:1|max:8',
            'guests' => 'nullable|array',
            'guests.*.name' => 'nullable|string|max:255',
            'guests.*.surname' => 'nullable|string|max:255',
            'guests.*.email' => 'nullable|email|max:255',
        ];

        $messages = [
            'restaurant_id.required' => 'Please select a restaurant.',
            'restaurant_id.exists' => 'The selected restaurant is not valid.',
            'reservation_name.required' => 'Your first name is required.',
            'reservation_surname.required' => 'Your last name is required.',
            'email.email' => 'Please enter a valid email address.',
            'reservation_date.required' => 'Reservation date is required.',
            'reservation_date.after_or_equal' => 'Reservation date must be today or a future date.',
            'reservation_time.required' => 'Reservation time is required.',
            'reservation_time.date_format' => 'Please enter a valid time format (HH:MM).',
            'duration_hours.required' => 'Duration is required.',
            'duration_hours.min' => 'Duration must be at least 1 hour.',
            'duration_hours.max' => 'Duration cannot exceed 8 hours.',
        ];

        return Validator::make($data, $rules, $messages);
    }
}
