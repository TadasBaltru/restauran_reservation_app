<?php

namespace App\Services;

use App\Models\Table;
use App\Models\Reservation;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class TableAllocationService
{
    public function allocateTables(int $restaurantId, int $numberOfPeople, Carbon $startTime, int $durationHours): ?array
    {
        // Step 1: Get available tables (not reserved during requested time)
        $availableTables = $this->getAvailableTables($restaurantId, $startTime, $durationHours);

        if ($availableTables->isEmpty()) {
            return null;
        }

        // Step 2: Filter tables that can accommodate at least min_people_count
        $viableTables = $availableTables->filter(function ($table) use ($numberOfPeople) {
            return $table->min_people_count <= $numberOfPeople;
        });

        if ($viableTables->isEmpty()) {
            return null;
        }

        // Step 3: Try to find optimal allocation using different strategies
        return $this->findOptimalAllocation($viableTables, $numberOfPeople);
    }


    private function getAvailableTables(int $restaurantId, Carbon $startTime, int $durationHours): Collection
    {
        $endTime = $startTime->copy()->addHours($durationHours);

        // Get all tables for the restaurant
        $allTables = Table::where('restaurant_id', $restaurantId)->get();

        // Filter out tables that have conflicting reservations
        return $allTables->filter(function ($table) use ($startTime, $endTime) {
            return !$this->hasConflictingReservation($table, $startTime, $endTime);
        });
    }


    private function hasConflictingReservation(Table $table, Carbon $startTime, Carbon $endTime): bool
    {
        return $table->reservations()
            ->where(function ($query) use ($startTime, $endTime) {
                // Check for overlapping reservations
                $query->where(function ($subQuery) use ($startTime, $endTime) {
                    // New reservation starts before existing ends AND ends after existing starts
                    $subQuery->where('reservation_date', '<', $endTime)
                        ->whereRaw('DATE_ADD(reservation_date, INTERVAL duration_hours HOUR) > ?', [$startTime]);
                });
            })
            ->exists();
    }


    private function findOptimalAllocation(Collection $viableTables, int $numberOfPeople): ?array
    {
        // Strategy 1: Perfect single table match (max_people_count == number_of_people)
        $perfectMatch = $this->findPerfectSingleTableMatch($viableTables, $numberOfPeople);
        if ($perfectMatch) {
            return $perfectMatch;
        }

        // Strategy 2: Best combination with minimal waste using backtracking
        $bestCombination = $this->findBestCombination($viableTables, $numberOfPeople);
        if ($bestCombination) {
            return $bestCombination;
        }

        // Strategy 3: Fallback - single table where min_people_count == number_of_people
        $minMatch = $this->findMinimumSingleTableMatch($viableTables, $numberOfPeople);
        if ($minMatch) {
            return $minMatch;
        }

        // No valid allocation found
        return null;
    }


    private function findPerfectSingleTableMatch(Collection $tables, int $numberOfPeople): ?array
    {
        $perfectTable = $tables->first(function ($table) use ($numberOfPeople) {
            return $table->max_people_count === $numberOfPeople;
        });

        if ($perfectTable) {
            return [
                'tables' => [
                    [
                        'table_id' => $perfectTable->id,
                        'table_name' => $perfectTable->name,
                        'people_seated' => $numberOfPeople,
                        'table_capacity' => $perfectTable->max_people_count
                    ]
                ],
                'total_people_seated' => $numberOfPeople,
                'total_wasted_seats' => 0,
                'allocation_strategy' => 'perfect_single_match'
            ];
        }

        return null;
    }

    private function findBestCombination(Collection $tables, int $numberOfPeople): ?array
    {
        $sortedTables = $tables->sortByDesc('max_people_count')->values();
        $bestAllocation = null;
        $minWaste = PHP_INT_MAX;
        $minTables = PHP_INT_MAX;

        // Use backtracking to find all valid combinations
        $this->backtrackAllocation(
            $sortedTables->toArray(),
            $numberOfPeople,
            [],
            0,
            $bestAllocation,
            $minWaste,
            $minTables
        );

        return $bestAllocation;
    }


    private function backtrackAllocation(
        array $tables,
        int $remainingPeople,
        array $currentAllocation,
        int $tableIndex,
        ?array &$bestAllocation,
        int &$minWaste,
        int &$minTables
    ): void {
        // Base case: all people are seated
        if ($remainingPeople <= 0) {
            $totalCapacity = array_sum(array_column($currentAllocation, 'table_capacity'));
            $totalSeated = array_sum(array_column($currentAllocation, 'people_seated'));
            $waste = $totalCapacity - $totalSeated;
            $tableCount = count($currentAllocation);

            // Check if this is a better allocation
            if ($this->isBetterAllocation($waste, $tableCount, $minWaste, $minTables)) {
                $bestAllocation = [
                    'tables' => $currentAllocation,
                    'total_people_seated' => $totalSeated,
                    'total_wasted_seats' => $waste,
                    'allocation_strategy' => 'optimal_combination'
                ];
                $minWaste = $waste;
                $minTables = $tableCount;
            }
            return;
        }

        // Try each remaining table
        for ($i = $tableIndex; $i < count($tables); $i++) {
            $table = $tables[$i];

            // Skip if this table can't seat anyone
            if ($table['min_people_count'] > $remainingPeople) {
                continue;
            }

            // Calculate how many people to seat at this table
            $peopleAtTable = min($remainingPeople, $table['max_people_count']);

            // Add this table to current allocation
            $newAllocation = $currentAllocation;
            $newAllocation[] = [
                'table_id' => $table['id'],
                'table_name' => $table['name'],
                'people_seated' => $peopleAtTable,
                'table_capacity' => $table['max_people_count']
            ];

            // Pruning: skip if current allocation already uses more tables than best found
            if (
                count($newAllocation) < $minTables ||
                (count($newAllocation) === $minTables &&
                 array_sum(array_column($newAllocation, 'table_capacity')) - array_sum(array_column($newAllocation, 'people_seated')) < $minWaste)
            ) {
                // Continue with remaining people
                $this->backtrackAllocation(
                    $tables,
                    $remainingPeople - $peopleAtTable,
                    $newAllocation,
                    $i + 1, // Don't reuse the same table
                    $bestAllocation,
                    $minWaste,
                    $minTables
                );
            }
        }
    }

    private function isBetterAllocation(int $waste, int $tableCount, int $minWaste, int $minTables): bool
    {
        // Prefer fewer tables first, then less waste
        return $tableCount < $minTables ||
               ($tableCount === $minTables && $waste < $minWaste);
    }

    /**
     * Strategy 3: Fallback - find single table where min_people_count == number_of_people.
     *
     * @param Collection $tables
     * @param int $numberOfPeople
     * @return array|null
     */
    private function findMinimumSingleTableMatch(Collection $tables, int $numberOfPeople): ?array
    {
        $minTable = $tables->first(function ($table) use ($numberOfPeople) {
            return $table->min_people_count === $numberOfPeople &&
                   $table->max_people_count >= $numberOfPeople;
        });

        if ($minTable) {
            $wastedSeats = $minTable->max_people_count - $numberOfPeople;

            return [
                'tables' => [
                    [
                        'table_id' => $minTable->id,
                        'table_name' => $minTable->name,
                        'people_seated' => $numberOfPeople,
                        'table_capacity' => $minTable->max_people_count
                    ]
                ],
                'total_people_seated' => $numberOfPeople,
                'total_wasted_seats' => $wastedSeats,
                'allocation_strategy' => 'minimum_single_match'
            ];
        }

        return null;
    }

    public function getAllocationSummary(array $allocation): string
    {
        $tableCount = count($allocation['tables']);
        $strategy = $allocation['allocation_strategy'];
        $waste = $allocation['total_wasted_seats'];

        $summary = "Allocated {$tableCount} table(s) using '{$strategy}' strategy";

        if ($waste > 0) {
            $summary .= " with {$waste} wasted seat(s)";
        }

        return $summary;
    }
}
