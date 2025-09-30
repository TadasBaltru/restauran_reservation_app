<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\TableAllocationService;
use App\Models\Restaurant;
use App\Models\Table;
use App\Models\Reservation;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TableAllocationServiceTest extends TestCase
{
    use RefreshDatabase;

    protected TableAllocationService $allocationService;
    protected Restaurant $restaurant;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->allocationService = new TableAllocationService();
        
        // Create a test restaurant
        $this->restaurant = Restaurant::factory()->create([
            'name' => 'Test Restaurant'
        ]);
    }

    /** @test */
    public function it_allocates_perfect_single_table_match()
    {
        // Arrange: Create tables with different capacities
        $table1 = Table::factory()->create([
            'restaurant_id' => $this->restaurant->id,
            'name' => 'Table 1',
            'min_people_count' => 2,
            'max_people_count' => 4
        ]);

        $table2 = Table::factory()->create([
            'restaurant_id' => $this->restaurant->id,
            'name' => 'Table 2',
            'min_people_count' => 4,
            'max_people_count' => 6
        ]);

        // Act: Request allocation for exactly 4 people
        $startTime = Carbon::now()->addHour();
        $allocation = $this->allocationService->allocateTables(
            $this->restaurant->id,
            4, // number of people
            $startTime,
            2 // duration hours
        );

        // Assert: Should get perfect match with Table 1
        $this->assertNotNull($allocation);
        $this->assertEquals('perfect_single_match', $allocation['allocation_strategy']);
        $this->assertEquals(1, count($allocation['tables']));
        $this->assertEquals($table1->id, $allocation['tables'][0]['table_id']);
        $this->assertEquals(4, $allocation['tables'][0]['people_seated']);
        $this->assertEquals(0, $allocation['total_wasted_seats']);
    }

    /** @test */
    public function it_allocates_optimal_multiple_tables()
    {
        // Arrange: Create smaller tables that need to be combined
        Table::factory()->create([
            'restaurant_id' => $this->restaurant->id,
            'name' => 'Table 1',
            'min_people_count' => 2,
            'max_people_count' => 3
        ]);

        Table::factory()->create([
            'restaurant_id' => $this->restaurant->id,
            'name' => 'Table 2',
            'min_people_count' => 2,
            'max_people_count' => 3
        ]);

        Table::factory()->create([
            'restaurant_id' => $this->restaurant->id,
            'name' => 'Table 3',
            'min_people_count' => 1,
            'max_people_count' => 2
        ]);

        // Act: Request allocation for 5 people
        $startTime = Carbon::now()->addHour();
        $allocation = $this->allocationService->allocateTables(
            $this->restaurant->id,
            5,
            $startTime,
            2
        );

        // Assert: Should combine tables optimally
        $this->assertNotNull($allocation);
        $this->assertEquals('optimal_combination', $allocation['allocation_strategy']);
        $this->assertEquals(5, $allocation['total_people_seated']);
        $this->assertLessThanOrEqual(2, count($allocation['tables'])); // Should use minimal tables
    }

    /** @test */
    public function it_uses_minimum_match_as_fallback()
    {
        // Arrange: Create tables where only minimum match is possible
        // Create a table with min = 3 and max = 8
        Table::factory()->create([
            'restaurant_id' => $this->restaurant->id,
            'name' => 'Large Table',
            'min_people_count' => 3,
            'max_people_count' => 8
        ]);
        
        // Create another large table that can't be used (min too high)
        Table::factory()->create([
            'restaurant_id' => $this->restaurant->id,
            'name' => 'VIP Table',
            'min_people_count' => 6,
            'max_people_count' => 10
        ]);

        // Act: Request allocation for exactly min_people_count of the first table
        $startTime = Carbon::now()->addHour();
        $allocation = $this->allocationService->allocateTables(
            $this->restaurant->id,
            3,
            $startTime,
            2
        );

        // Assert: Should find optimal allocation using the large table
        $this->assertNotNull($allocation);
        $this->assertEquals('optimal_combination', $allocation['allocation_strategy']);
        $this->assertEquals(1, count($allocation['tables']));
        $this->assertEquals(3, $allocation['tables'][0]['people_seated']);
        $this->assertEquals(5, $allocation['total_wasted_seats']); // 8 - 3 = 5
    }

    /** @test */
    public function it_returns_null_when_no_allocation_possible()
    {
        // Arrange: Create small tables
        Table::factory()->create([
            'restaurant_id' => $this->restaurant->id,
            'name' => 'Small Table',
            'min_people_count' => 2,
            'max_people_count' => 2
        ]);

        // Act: Request allocation for more people than any table can handle
        $startTime = Carbon::now()->addHour();
        $allocation = $this->allocationService->allocateTables(
            $this->restaurant->id,
            10, // Too many people
            $startTime,
            2
        );

        // Assert: Should return null
        $this->assertNull($allocation);
    }

    /** @test */
    public function it_excludes_reserved_tables_from_allocation()
    {
        // Arrange: Create tables
        $availableTable = Table::factory()->create([
            'restaurant_id' => $this->restaurant->id,
            'name' => 'Available Table',
            'min_people_count' => 2,
            'max_people_count' => 4
        ]);

        $reservedTable = Table::factory()->create([
            'restaurant_id' => $this->restaurant->id,
            'name' => 'Reserved Table',
            'min_people_count' => 2,
            'max_people_count' => 4
        ]);

        // Create existing reservation on reserved table
        $existingReservation = Reservation::factory()->create([
            'restaurant_id' => $this->restaurant->id,
            'reservation_date' => Carbon::now()->addHours(2),
            'duration_hours' => 2
        ]);
        $existingReservation->tables()->attach($reservedTable->id);

        // Act: Request allocation during conflicting time
        $startTime = Carbon::now()->addHours(2)->addMinutes(30); // Overlaps with existing
        $allocation = $this->allocationService->allocateTables(
            $this->restaurant->id,
            4,
            $startTime,
            2
        );

        // Assert: Should only use available table
        $this->assertNotNull($allocation);
        $this->assertEquals($availableTable->id, $allocation['tables'][0]['table_id']);
        $this->assertEquals(1, count($allocation['tables']));
    }

    /** @test */
    public function it_handles_complex_combination_scenario()
    {
        // Arrange: Create various table sizes
        Table::factory()->create([
            'restaurant_id' => $this->restaurant->id,
            'name' => 'Table A',
            'min_people_count' => 1,
            'max_people_count' => 2
        ]);

        Table::factory()->create([
            'restaurant_id' => $this->restaurant->id,
            'name' => 'Table B',
            'min_people_count' => 2,
            'max_people_count' => 4
        ]);

        Table::factory()->create([
            'restaurant_id' => $this->restaurant->id,
            'name' => 'Table C',
            'min_people_count' => 4,
            'max_people_count' => 6
        ]);

        Table::factory()->create([
            'restaurant_id' => $this->restaurant->id,
            'name' => 'Table D',
            'min_people_count' => 1,
            'max_people_count' => 3
        ]);

        // Act: Request allocation for 7 people
        $startTime = Carbon::now()->addHour();
        $allocation = $this->allocationService->allocateTables(
            $this->restaurant->id,
            7,
            $startTime,
            2
        );

        // Assert: Should find a valid combination
        $this->assertNotNull($allocation);
        $this->assertEquals(7, $allocation['total_people_seated']);
        $this->assertGreaterThan(0, count($allocation['tables']));
        
        // Verify allocation strategy makes sense
        $totalCapacity = array_sum(array_column($allocation['tables'], 'table_capacity'));
        $this->assertGreaterThanOrEqual(7, $totalCapacity);
    }

    /** @test */
    public function it_optimizes_for_minimal_waste()
    {
        // Arrange: Create tables that could lead to different waste scenarios
        Table::factory()->create([
            'restaurant_id' => $this->restaurant->id,
            'name' => 'Efficient Table',
            'min_people_count' => 3,
            'max_people_count' => 5
        ]);

        Table::factory()->create([
            'restaurant_id' => $this->restaurant->id,
            'name' => 'Large Table',
            'min_people_count' => 2,
            'max_people_count' => 8
        ]);

        // Act: Request allocation for 5 people
        $startTime = Carbon::now()->addHour();
        $allocation = $this->allocationService->allocateTables(
            $this->restaurant->id,
            5,
            $startTime,
            2
        );

        // Assert: Should choose the efficient table (0 waste) over large table (3 waste)
        $this->assertNotNull($allocation);
        $this->assertEquals(0, $allocation['total_wasted_seats']);
        $this->assertEquals('Efficient Table', $allocation['tables'][0]['table_name']);
    }

    /** @test */
    public function it_provides_meaningful_allocation_summary()
    {
        // Arrange
        $table = Table::factory()->create([
            'restaurant_id' => $this->restaurant->id,
            'name' => 'Test Table',
            'min_people_count' => 2,
            'max_people_count' => 4
        ]);

        $startTime = Carbon::now()->addHour();
        $allocation = $this->allocationService->allocateTables(
            $this->restaurant->id,
            4,
            $startTime,
            2
        );

        // Act
        $summary = $this->allocationService->getAllocationSummary($allocation);

        // Assert
        $this->assertStringContainsString('1 table(s)', $summary);
        $this->assertStringContainsString('perfect_single_match', $summary);
        $this->assertStringNotContainsString('wasted', $summary); // No waste for perfect match
    }

    /** @test */
    public function it_handles_edge_case_of_single_person()
    {
        // Arrange
        Table::factory()->create([
            'restaurant_id' => $this->restaurant->id,
            'name' => 'Bar Seat',
            'min_people_count' => 1,
            'max_people_count' => 1
        ]);

        Table::factory()->create([
            'restaurant_id' => $this->restaurant->id,
            'name' => 'Regular Table',
            'min_people_count' => 2,
            'max_people_count' => 4
        ]);

        // Act: Request allocation for 1 person
        $startTime = Carbon::now()->addHour();
        $allocation = $this->allocationService->allocateTables(
            $this->restaurant->id,
            1,
            $startTime,
            2
        );

        // Assert: Should get the bar seat (perfect match)
        $this->assertNotNull($allocation);
        $this->assertEquals('perfect_single_match', $allocation['allocation_strategy']);
        $this->assertEquals('Bar Seat', $allocation['tables'][0]['table_name']);
        $this->assertEquals(0, $allocation['total_wasted_seats']);
    }
}
